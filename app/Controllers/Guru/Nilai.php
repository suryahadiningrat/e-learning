<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\NilaiModel;
use App\Models\JadwalModel;
use App\Models\JurusanModel;
use App\Models\SiswaModel;

class Nilai extends BaseController
{
    protected $nilaiModel;
    protected $jadwalModel;
    protected $jurusanModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->jadwalModel = new JadwalModel();
        $this->jurusanModel = new JurusanModel();
        $this->siswaModel = new SiswaModel();
        $this->guruId = (new GuruModel)->getGuruByUserId(session('user_id'));
    }

    // Step 1: Tampilkan daftar jurusan yang memiliki mata pelajaran yang diajar guru
    public function index()
    {
        $guruId = $this->guruId;
        // Get jurusan yang memiliki mata pelajaran yang diajar guru
        $jurusan = $this->nilaiModel->getJurusanByGuru($guruId);

        $data = [
            'title' => 'Data Nilai',
            'jurusan' => $jurusan
        ];

        return view('guru/nilai/index', $data);
    }

    // Step 2: Tampilkan mata pelajaran yang diajar guru di jurusan tertentu
    public function mataPelajaran($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('guru/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        $guruId = $this->guruId;
        $jurusan = $this->jurusanModel->find($jurusanId);
        
        if (!$jurusan) {
            return redirect()->to('guru/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        // Get mata pelajaran yang diajar guru di jurusan tertentu
        $mataPelajaran = $this->nilaiModel->getMataPelajaranByGuruAndJurusan($guruId, $jurusanId);

        $data = [
            'title' => 'Mata Pelajaran - ' . $jurusan['nama_jurusan'],
            'jurusan' => $jurusan,
            'mata_pelajaran' => $mataPelajaran
        ];

        return view('guru/nilai/mata_pelajaran', $data);
    }

    // Step 3: Tampilkan form input nilai berdasarkan mata pelajaran
    public function inputNilai($jadwalId = null)
    {
        if (!$jadwalId) {
            return redirect()->to('guru/nilai')->with('error', 'Jadwal tidak ditemukan');
        }

        $guruId = $this->guruId;

        // Get jadwal info dengan jurusan_id dan verifikasi guru
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal j')
                    ->select('j.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, jur.nama_jurusan, mp.nama as nama_mata_pelajaran')
                    ->join('kelas k', 'k.id = j.kelas_id')
                    ->join('jurusan jur', 'jur.id = k.jurusan_id')
                    ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                    ->where('j.id', $jadwalId)
                    ->where('j.guru_id', $guruId) // Pastikan guru yang mengajar
                    ->get()
                    ->getRowArray();

        if (!$jadwal) {
            return redirect()->to('guru/nilai')->with('error', 'Jadwal tidak ditemukan atau Anda tidak berhak mengakses');
        }

        // Get siswa berdasarkan kelas di jadwal
        $siswa = $this->nilaiModel->getSiswaByJadwal($jadwalId);

        // Get nilai yang sudah ada dan format untuk frontend
        $nilaiExisting = $this->nilaiModel->getNilaiByJadwal($jadwalId);
        $nilaiFormatted = [];
        
        foreach ($nilaiExisting as $nilai) {
            $nilaiFormatted[$nilai['siswa_id']] = [
                'tugas' => json_decode($nilai['nilai_tugas'], true) ?: [],
                'ulangan' => json_decode($nilai['nilai_ulangan'], true) ?: [],
                'uts' => $nilai['nilai_uts'],
                'uas' => $nilai['nilai_uas']
            ];
        }

        $data = [
            'title' => 'Input Nilai - ' . $jadwal['nama_mata_pelajaran'],
            'jadwal' => $jadwal,
            'siswa' => $siswa,
            'nilai_existing' => $nilaiFormatted
        ];

        return view('guru/nilai/input_nilai', $data);
    }

    // Step 4: Simpan nilai
    public function store()
    {
        $jadwalId = $this->request->getPost('jadwal_id');
        $jurusanId = $this->request->getPost('jurusan_id');
        $guruId = $this->guruId;
        $nilaiData = $this->request->getPost('nilai');

        // Verifikasi bahwa guru berhak mengajar jadwal ini
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal')
                    ->where('id', $jadwalId)
                    ->where('guru_id', $guruId)
                    ->get()
                    ->getRowArray();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Anda tidak berhak mengakses jadwal ini');
        }

        // Debug: Log data yang diterima
        log_message('debug', 'Jadwal ID: ' . $jadwalId);
        log_message('debug', 'Jurusan ID: ' . $jurusanId);
        log_message('debug', 'Nilai Data: ' . json_encode($nilaiData));

        if (!$nilaiData || !is_array($nilaiData)) {
            return redirect()->back()->with('error', 'Data nilai tidak valid');
        }

        $dataToSave = [];

        foreach ($nilaiData as $siswaId => $nilai) {
            // Filter nilai tugas yang tidak kosong
            $nilaiTugas = [];
            if (isset($nilai['tugas']) && is_array($nilai['tugas'])) {
                foreach ($nilai['tugas'] as $tugas) {
                    if (!empty($tugas) && $tugas !== '' && $tugas !== null) {
                        $nilaiTugas[] = floatval($tugas);
                    }
                }
            }

            // Filter nilai ulangan yang tidak kosong
            $nilaiUlangan = [];
            if (isset($nilai['ulangan']) && is_array($nilai['ulangan'])) {
                foreach ($nilai['ulangan'] as $ulangan) {
                    if (!empty($ulangan) && $ulangan !== '' && $ulangan !== null) {
                        $nilaiUlangan[] = floatval($ulangan);
                    }
                }
            }

            $dataToSave[] = [
                'siswa_id' => $siswaId,
                'jadwal_id' => $jadwalId,
                'nilai_tugas' => $nilaiTugas,
                'nilai_ulangan' => $nilaiUlangan,
                'nilai_uts' => !empty($nilai['uts']) ? floatval($nilai['uts']) : null,
                'nilai_uas' => !empty($nilai['uas']) ? floatval($nilai['uas']) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        // Debug: Log data yang akan disimpan
        log_message('debug', 'Data to save: ' . json_encode($dataToSave));

        try {
            $result = $this->nilaiModel->saveNilaiBatch($dataToSave);
            log_message('debug', 'Save result: ' . ($result ? 'success' : 'failed'));
            
            if ($result) {
                // Redirect back ke halaman input nilai yang sama
                return redirect()->to('guru/nilai/input/' . $jadwalId)
                               ->with('success', 'Nilai berhasil disimpan');
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan nilai');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saving nilai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    // Export nilai untuk guru
    public function export($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('guru/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        $guruId = $this->guruId;
        $jurusan = $this->jurusanModel->find($jurusanId);
        
        if (!$jurusan) {
            return redirect()->to('guru/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        // Get nilai yang diajar oleh guru di jurusan tertentu
        $nilai = $this->nilaiModel->getNilaiByGuruAndJurusan($guruId, $jurusanId);

        // Hitung jumlah maksimal tugas dan ulangan
        $maxTugas = 0;
        $maxUlangan = 0;
        foreach ($nilai as $item) {
            $tugasArray = json_decode($item['nilai_tugas'], true) ?: [];
            $ulanganArray = json_decode($item['nilai_ulangan'], true) ?: [];
            $maxTugas = max($maxTugas, count($tugasArray));
            $maxUlangan = max($maxUlangan, count($ulanganArray));
        }

        // Export to Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'DATA NILAI - ' . strtoupper($jurusan['nama_jurusan']) . ' (GURU)');
        $sheet->mergeCells('A1:' . chr(65 + 3 + $maxTugas + $maxUlangan) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Set headers
        $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran'];
        
        // Add tugas headers
        for ($i = 1; $i <= $maxTugas; $i++) {
            $headers[] = "Tugas $i";
        }
        
        // Add ulangan headers
        for ($i = 1; $i <= $maxUlangan; $i++) {
            $headers[] = "Ulangan $i";
        }
        
        $headers[] = 'UTS';
        $headers[] = 'UAS';

        $col = 'A';
        $row = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $col++;
        }

        // Set data
        $row = 4;
        $no = 1;
        foreach ($nilai as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['nis']);
            $sheet->setCellValue('C' . $row, $item['nama_siswa']);
            $sheet->setCellValue('D' . $row, $item['nama_kelas']);
            $sheet->setCellValue('E' . $row, $item['nama_mata_pelajaran']);
            
            $col = 'F'; // Start from F (after mata pelajaran)
            
            // Format nilai tugas - separate columns
            $nilaiTugas = json_decode($item['nilai_tugas'], true) ?: [];
            for ($i = 0; $i < $maxTugas; $i++) {
                $sheet->setCellValue($col . $row, isset($nilaiTugas[$i]) ? $nilaiTugas[$i] : '');
                $col++;
            }
            
            // Format nilai ulangan - separate columns
            $nilaiUlangan = json_decode($item['nilai_ulangan'], true) ?: [];
            for ($i = 0; $i < $maxUlangan; $i++) {
                $sheet->setCellValue($col . $row, isset($nilaiUlangan[$i]) ? $nilaiUlangan[$i] : '');
                $col++;
            }
            
            $sheet->setCellValue($col . $row, $item['nilai_uts']);
            $col++;
            $sheet->setCellValue($col . $row, $item['nilai_uas']);
            
            $row++;
            $no++;
        }

        // Auto size columns
        $totalCols = 5 + $maxTugas + $maxUlangan + 2; // 5 basic + tugas + ulangan + UTS + UAS
        for ($i = 0; $i < $totalCols; $i++) {
            $sheet->getColumnDimension(chr(65 + $i))->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Nilai_Guru_' . $jurusan['nama_jurusan'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
} 