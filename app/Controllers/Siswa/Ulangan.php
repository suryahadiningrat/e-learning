<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\UlanganModel;
use App\Models\SiswaModel;

class Ulangan extends BaseController
{
    protected $ulanganModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->ulanganModel = new UlanganModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Get siswa data
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Get ulangan yang tersedia untuk kelas siswa
        $ulangan = $this->ulanganModel->getUlanganByKelas($siswa['kelas_id']);
        
        // Check which ulangan sudah dikerjakan
        foreach ($ulangan as &$item) {
            $item['sudah_mengerjakan'] = $this->ulanganModel->checkSiswaSudahMengerjakan($item['id'], $siswa['id']);
        }
        
        $data = [
            'title' => 'Ulangan',
            'ulangan' => $ulangan,
            'siswa' => $siswa
        ];

        return view('siswa/ulangan/index', $data);
    }

    public function kerjakan($ulanganId = null)
    {
        $userId = session('user_id');
        
        // Get siswa data
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Get ulangan detail
        $ulangan = $this->ulanganModel->getUlanganById($ulanganId);
        
        if (!$ulangan) {
            return redirect()->to('siswa/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }
        
        // Check if ulangan is for siswa's class
        if ($ulangan['kelas_id'] != $siswa['kelas_id']) {
            return redirect()->to('siswa/ulangan')->with('error', 'Anda tidak berhak mengakses ulangan ini');
        }
        
        // Check if ulangan is published
        if ($ulangan['status'] != 'published') {
            return redirect()->to('siswa/ulangan')->with('error', 'Ulangan belum dipublish');
        }
        
        // Check if current time is within ulangan time
        $now = time();
        $waktuMulai = strtotime($ulangan['waktu_mulai']);

        $waktuSelesai = strtotime($ulangan['waktu_selesai']);
        
        if ($now < $waktuMulai) {
            return redirect()->to('siswa/ulangan')->with('error', 'Ulangan belum dimulai');
        }
        
        if ($now > $waktuSelesai) {
            return redirect()->to('siswa/ulangan')->with('error', 'Ulangan sudah berakhir');
        }
        
        // Check if siswa sudah mengerjakan ulangan ini
        $sudahMengerjakan = $this->ulanganModel->checkSiswaSudahMengerjakan($ulanganId, $siswa['id']);
        
        if ($sudahMengerjakan) {
            return redirect()->to('siswa/ulangan/hasil/' . $ulanganId)->with('error', 'Anda sudah mengerjakan ulangan ini');
        }
        
        $data = [
            'title' => 'Mengerjakan Ulangan',
            'ulangan' => $ulangan,
            'siswa' => $siswa
        ];

        return view('siswa/ulangan/kerjakan', $data);
    }

    public function saveJawaban()
    {
        $userId = session('user_id');
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan']);
        }
        
        $ulanganId = $this->request->getPost('ulangan_id');
        $jawaban = $this->request->getPost('jawaban');
        
        // Save jawaban to session for temporary storage
        session()->set('jawaban_temp_' . $ulanganId, $jawaban);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Jawaban berhasil disimpan']);
    }

    public function submitJawaban()
    {
        $userId = session('user_id');
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return redirect()->to('siswa/ulangan')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $ulanganId = $this->request->getPost('ulangan_id');
        $jawaban = $this->request->getPost('jawaban');
        
        // Get ulangan detail
        $ulangan = $this->ulanganModel->getUlanganById($ulanganId);
        
        if (!$ulangan) {
            return redirect()->to('siswa/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }
        
        // Calculate score
        $score = $this->calculateScore($ulangan, $jawaban);
        
        // Save hasil ulangan
        $data = [
            'ulangan_id' => $ulanganId,
            'siswa_id' => $siswa['id'],
            'jawaban_json' => json_encode($jawaban),
            'nilai' => $score,
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->ulanganModel->saveHasilUlangan($data);
        
        // Clear temporary jawaban
        session()->remove('jawaban_temp_' . $ulanganId);
        
        return redirect()->to('siswa/ulangan/hasil/' . $ulanganId)->with('success', 'Ulangan berhasil diselesaikan');
    }

    public function hasil($ulanganId = null)
    {
        $userId = session('user_id');
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Get ulangan detail
        $ulangan = $this->ulanganModel->getUlanganById($ulanganId);
        
        if (!$ulangan) {
            return redirect()->to('siswa/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }
        
        // Get hasil ulangan siswa
        $hasil = $this->ulanganModel->getHasilUlanganSiswa($ulanganId, $siswa['id']);
        
        if (!$hasil) {
            return redirect()->to('siswa/ulangan')->with('error', 'Hasil ulangan tidak ditemukan');
        }
        
        $data = [
            'title' => 'Hasil Ulangan',
            'ulangan' => $ulangan,
            'hasil' => $hasil,
            'siswa' => $siswa
        ];

        return view('siswa/ulangan/hasil', $data);
    }

    public function riwayat()
    {
        $userId = session('user_id');
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Get riwayat ulangan siswa
        $riwayat = $this->ulanganModel->getRiwayatUlanganSiswa($siswa['id']);
        
        $data = [
            'title' => 'Riwayat Ulangan',
            'riwayat' => $riwayat,
            'siswa' => $siswa
        ];

        return view('siswa/ulangan/riwayat', $data);
    }

    private function calculateScore($ulangan, $jawaban)
    {
        $soalJson = json_decode($ulangan['soal_json'], true);
        $totalScore = 0;
        $totalBobot = 0;
        
        if (isset($soalJson['soal'])) {
            foreach ($soalJson['soal'] as $index => $soal) {
                $totalBobot += $soal['bobot'];
                
                if ($soal['tipe'] == 'pilihan_ganda') {
                    if (isset($jawaban[$index]) && $jawaban[$index] == $soal['jawaban_benar']) {
                        $totalScore += $soal['bobot'];
                    }
                } else {
                    // For essay, give full score (will be reviewed by teacher later)
                    $totalScore += $soal['bobot'];
                }
            }
        }
        
        return $totalScore;
    }
} 