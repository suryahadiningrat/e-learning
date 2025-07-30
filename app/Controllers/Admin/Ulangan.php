<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UlanganModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class Ulangan extends BaseController
{
    protected $ulanganModel;
    protected $mataPelajaranModel;
    protected $kelasModel;
    protected $db;

    public function __construct()
    {
        $this->ulanganModel = new UlanganModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->kelasModel = new KelasModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Ulangan',
            'ulangan' => $this->ulanganModel->getUlanganWithRelations()
        ];

        return view('admin/ulangan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Ulangan',
            'kelas' => $this->kelasModel->findAll(),
            'mata_pelajaran' => $this->mataPelajaranModel->getAktifMataPelajaran()
        ];

        return view('admin/ulangan/create', $data);
    }

    public function store()
    {
        // Validasi input
        $rules = [
            'judul_ulangan' => 'required|min_length[3]|max_length[255]',
            'mata_pelajaran_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'waktu_mulai' => 'required|valid_date',
            'waktu_selesai' => 'required|valid_date',
            'durasi_menit' => 'required|numeric|greater_than[0]',
            'soal_json' => 'required'
        ];

        $messages = [
            'judul_ulangan' => [
                'required' => 'Judul ulangan harus diisi',
                'min_length' => 'Judul minimal 3 karakter',
                'max_length' => 'Judul maksimal 255 karakter'
            ],
            'mata_pelajaran_id' => [
                'required' => 'Mata pelajaran harus dipilih',
                'numeric' => 'Mata pelajaran tidak valid'
            ],
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas harus berupa angka'
            ],
            'waktu_mulai' => [
                'required' => 'Waktu mulai harus diisi',
                'valid_date' => 'Format waktu mulai tidak valid'
            ],
            'waktu_selesai' => [
                'required' => 'Waktu selesai harus diisi',
                'valid_date' => 'Format waktu selesai tidak valid'
            ],
            'durasi_menit' => [
                'required' => 'Durasi harus diisi',
                'numeric' => 'Durasi harus berupa angka',
                'greater_than' => 'Durasi harus lebih dari 0'
            ],
            'soal_json' => [
                'required' => 'Soal harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi waktu
        $waktuMulai = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');
        
        if (strtotime($waktuMulai) >= strtotime($waktuSelesai)) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih besar dari waktu mulai');
        }

        // Validasi JSON soal
        $soalJson = $this->request->getPost('soal_json');
        $soalArray = json_decode($soalJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withInput()->with('error', 'Format soal tidak valid');
        }

        if (empty($soalArray) || !isset($soalArray['soal']) || empty($soalArray['soal'])) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus ada 1 soal');
        }

        // Simpan data ulangan
        $data = [
            'judul_ulangan' => $this->request->getPost('judul_ulangan'),
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'soal_json' => $soalJson,
            'created_by' => session('user_id'),
            'status' => 'draft'
        ];

        if ($this->ulanganModel->insert($data)) {
            return redirect()->to('admin/ulangan')->with('success', 'Ulangan berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan ulangan');
        }
    }

    public function edit($id = null)
    {
        $ulangan = $this->ulanganModel->getUlanganWithRelations($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Ulangan',
            'ulangan' => $ulangan,
            'kelas' => $this->kelasModel->findAll(),
            'mata_pelajaran' => $this->mataPelajaranModel->getAktifMataPelajaran()
        ];

        return view('admin/ulangan/edit', $data);
    }

    public function update($id = null)
    {
        $ulangan = $this->ulanganModel->find($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Validasi input
        $rules = [
            'judul_ulangan' => 'required|min_length[3]|max_length[255]',
            'mata_pelajaran_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'waktu_mulai' => 'required|valid_date',
            'waktu_selesai' => 'required|valid_date',
            'durasi_menit' => 'required|numeric|greater_than[0]',
            'soal_json' => 'required'
        ];

        $messages = [
            'judul_ulangan' => [
                'required' => 'Judul ulangan harus diisi',
                'min_length' => 'Judul minimal 3 karakter',
                'max_length' => 'Judul maksimal 255 karakter'
            ],
            'mata_pelajaran_id' => [
                'required' => 'Mata pelajaran harus dipilih',
                'numeric' => 'Mata pelajaran tidak valid'
            ],
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas harus berupa angka'
            ],
            'waktu_mulai' => [
                'required' => 'Waktu mulai harus diisi',
                'valid_date' => 'Format waktu mulai tidak valid'
            ],
            'waktu_selesai' => [
                'required' => 'Waktu selesai harus diisi',
                'valid_date' => 'Format waktu selesai tidak valid'
            ],
            'durasi_menit' => [
                'required' => 'Durasi harus diisi',
                'numeric' => 'Durasi harus berupa angka',
                'greater_than' => 'Durasi harus lebih dari 0'
            ],
            'soal_json' => [
                'required' => 'Soal harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi waktu
        $waktuMulai = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');
        
        if (strtotime($waktuMulai) >= strtotime($waktuSelesai)) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih besar dari waktu mulai');
        }

        // Validasi JSON soal
        $soalJson = $this->request->getPost('soal_json');
        $soalArray = json_decode($soalJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withInput()->with('error', 'Format soal tidak valid');
        }

        if (empty($soalArray) || !isset($soalArray['soal']) || empty($soalArray['soal'])) {
            return redirect()->back()->withInput()->with('error', 'Minimal harus ada 1 soal');
        }

        // Update data ulangan
        $data = [
            'judul_ulangan' => $this->request->getPost('judul_ulangan'),
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'soal_json' => $soalJson
        ];

        if ($this->ulanganModel->update($id, $data)) {
            return redirect()->to('admin/ulangan')->with('success', 'Ulangan berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate ulangan');
        }
    }

    public function delete($id = null)
    {
        $ulangan = $this->ulanganModel->find($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Hapus jawaban ulangan terlebih dahulu
        // $this->jawabanUlanganModel->where('ulangan_id', $id)->delete(); // This line was removed as per the new_code, as JawabanUlanganModel is no longer imported.

        if ($this->ulanganModel->delete($id)) {
            return redirect()->to('admin/ulangan')->with('success', 'Ulangan berhasil dihapus');
        } else {
            return redirect()->to('admin/ulangan')->with('error', 'Gagal menghapus ulangan');
        }
    }

    public function publish($id = null)
    {
        $ulangan = $this->ulanganModel->find($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        if ($this->ulanganModel->update($id, ['status' => 'published'])) {
            return redirect()->to('admin/ulangan')->with('success', 'Ulangan berhasil dipublish');
        } else {
            return redirect()->to('admin/ulangan')->with('error', 'Gagal mempublish ulangan');
        }
    }

    public function close($id = null)
    {
        $ulangan = $this->ulanganModel->find($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        if ($this->ulanganModel->update($id, ['status' => 'closed'])) {
            return redirect()->to('admin/ulangan')->with('success', 'Ulangan berhasil ditutup');
        } else {
            return redirect()->to('admin/ulangan')->with('error', 'Gagal menutup ulangan');
        }
    }

    public function preview($id = null)
    {
        $ulangan = $this->ulanganModel->getUlanganWithRelations($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        $data = [
            'title' => 'Preview Ulangan',
            'ulangan' => $ulangan,
            'soal' => json_decode($ulangan['soal_json'], true)
        ];

        return view('admin/ulangan/preview', $data);
    }

    public function hasil($id = null)
    {
        $ulangan = $this->ulanganModel->getUlanganWithRelations($id);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Get hasil ulangan
        $hasil = $this->ulanganModel->getHasilUlangan($id);
        
        // Calculate statistics
        $totalSoal = 0;
        $totalBobot = 0;
        $nilaiArray = [];
        
        if (!empty($hasil)) {
            $soalJson = json_decode($ulangan['soal_json'], true);
            if (isset($soalJson['soal'])) {
                $totalSoal = count($soalJson['soal']);
                foreach ($soalJson['soal'] as $soal) {
                    $totalBobot += $soal['bobot'];
                }
            }
            
            foreach ($hasil as $item) {
                $nilaiArray[] = $item['nilai'];
            }
        }
        
        $rataRata = !empty($nilaiArray) ? array_sum($nilaiArray) / count($nilaiArray) : 0;
        $nilaiTertinggi = !empty($nilaiArray) ? max($nilaiArray) : 0;
        $nilaiTerendah = !empty($nilaiArray) ? min($nilaiArray) : 0;

        $data = [
            'title' => 'Hasil Ulangan',
            'ulangan' => $ulangan,
            'hasil' => $hasil,
            'totalSoal' => $totalSoal,
            'totalBobot' => $totalBobot,
            'rataRata' => $rataRata,
            'nilaiTertinggi' => $nilaiTertinggi,
            'nilaiTerendah' => $nilaiTerendah
        ];

        return view('admin/ulangan/hasil', $data);
    }

    public function detailHasil($ulanganId, $siswaId)
    {
        $ulangan = $this->ulanganModel->getUlanganWithRelations($ulanganId);
        
        if (!$ulangan) {
            return redirect()->to('admin/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Get detail hasil ulangan siswa
        $hasil = $this->ulanganModel->getDetailHasilUlangan($ulanganId, $siswaId);
        
        if (!$hasil) {
            return redirect()->to('admin/ulangan/hasil/' . $ulanganId)->with('error', 'Hasil ulangan tidak ditemukan');
        }

        // Get data siswa
        $siswa = $this->db->table('siswa s')
                         ->select('s.*, u.full_name as nama_siswa, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                         ->join('users u', 'u.id = s.user_id')
                         ->join('kelas k', 'k.id = s.kelas_id')
                         ->where('s.id', $siswaId)
                         ->get()
                         ->getRowArray();

        $data = [
            'title' => 'Detail Hasil Ulangan',
            'ulangan' => $ulangan,
            'hasil' => $hasil,
            'siswa' => $siswa
        ];

        return view('admin/ulangan/detail_hasil', $data);
    }
} 