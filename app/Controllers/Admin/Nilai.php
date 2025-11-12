<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\JurusanModel;
use App\Models\JadwalModel;
use App\Models\SiswaModel;
use App\Models\MataPelajaranModel;

class Nilai extends BaseController
{
    protected $nilaiModel;
    protected $jurusanModel;
    protected $jadwalModel;
    protected $siswaModel;
    protected $mataPelajaranModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->jurusanModel = new JurusanModel();
        $this->jadwalModel = new JadwalModel();
        $this->siswaModel = new SiswaModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
    }

    // Step 1: Tampilkan semua jurusan
    public function index()
    {
        $data = [
            'title' => 'Data Nilai',
            'jurusan' => $this->jurusanModel->findAll()
        ];

        return view('admin/nilai/index', $data);
    }

    // Step 2: Tampilkan mata pelajaran berdasarkan jurusan
    public function mataPelajaran($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('admin/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->to('admin/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        $mataPelajaran = $this->nilaiModel->getMataPelajaranByJurusan($jurusanId);

        $data = [
            'title' => 'Mata Pelajaran - ' . $jurusan['nama_jurusan'],
            'jurusan' => $jurusan,
            'mata_pelajaran' => $mataPelajaran
        ];

        return view('admin/nilai/mata_pelajaran', $data);
    }

    // Step 3: Tampilkan form input nilai berdasarkan mata pelajaran
    public function inputNilai($jadwalId = null)
    {
        if (!$jadwalId) {
            return redirect()->to('admin/nilai')->with('error', 'Jadwal tidak ditemukan');
        }

        // Get jadwal info dengan jurusan_id
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal j')
                    ->select('j.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, jur.nama_jurusan')
                    ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                    ->join('kelas k', 'k.id = j.kelas_id')
                    ->join('jurusan jur', 'jur.id = k.jurusan_id')
                    ->where('j.id', $jadwalId)
                    ->get()
                    ->getRowArray();

        if (!$jadwal) {
            return redirect()->to('admin/nilai')->with('error', 'Jadwal tidak ditemukan');
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
                'uts_sem1' => $nilai['nilai_uts_sem1'] ?? '',
                'uas_sem1' => $nilai['nilai_uas_sem1'] ?? '',
                'uts_sem2' => $nilai['nilai_uts_sem2'] ?? '',
                'uas_sem2' => $nilai['nilai_uas_sem2'] ?? ''
            ];
        }

        $data = [
            'title' => 'Input Nilai - ' . $jadwal['nama_mata_pelajaran'],
            'jadwal' => $jadwal,
            'siswa' => $siswa,
            'nilai_existing' => $nilaiFormatted
        ];

        return view('admin/nilai/input_nilai', $data);
    }

    // Step 4: Simpan nilai
    public function store()
    {
        $jadwalId = $this->request->getPost('jadwal_id');
        $jurusanId = $this->request->getPost('jurusan_id');
        $nilaiData = $this->request->getPost('nilai');

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

            // Get semester values
            $utsSem1 = isset($nilai['uts_sem1']) && $nilai['uts_sem1'] !== '' ? floatval($nilai['uts_sem1']) : null;
            $uasSem1 = isset($nilai['uas_sem1']) && $nilai['uas_sem1'] !== '' ? floatval($nilai['uas_sem1']) : null;
            $utsSem2 = isset($nilai['uts_sem2']) && $nilai['uts_sem2'] !== '' ? floatval($nilai['uts_sem2']) : null;
            $uasSem2 = isset($nilai['uas_sem2']) && $nilai['uas_sem2'] !== '' ? floatval($nilai['uas_sem2']) : null;

            $dataToSave[] = [
                'siswa_id' => $siswaId,
                'jadwal_id' => $jadwalId,
                'nilai_tugas' => $nilaiTugas,
                'nilai_ulangan' => $nilaiUlangan,
                'nilai_uts_sem1' => $utsSem1,
                'nilai_uas_sem1' => $uasSem1,
                'nilai_uts_sem2' => $utsSem2,
                'nilai_uas_sem2' => $uasSem2,
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
                return redirect()->to('admin/nilai/input/' . $jadwalId)
                               ->with('success', 'Nilai berhasil disimpan');
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan nilai');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saving nilai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    // View nilai per siswa
    public function viewNilai($siswaId = null)
    {
        if (!$siswaId) {
            return redirect()->to('admin/nilai')->with('error', 'Siswa tidak ditemukan');
        }

        $siswa = $this->siswaModel->getSiswaWithRelations($siswaId);
        if (!$siswa) {
            return redirect()->to('admin/nilai')->with('error', 'Siswa tidak ditemukan');
        }

        $nilai = $this->nilaiModel->getNilaiBySiswa($siswaId);

        $data = [
            'title' => 'Nilai Siswa - ' . $siswa['full_name'],
            'siswa' => $siswa,
            'nilai' => $nilai
        ];

        return view('admin/nilai/view_nilai', $data);
    }

    // Export nilai
    public function export($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('admin/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->to('admin/nilai')->with('error', 'Jurusan tidak ditemukan');
        }

        $nilai = $this->nilaiModel->getNilaiByJurusan($jurusanId);

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
        $sheet->setCellValue('A1', 'DATA NILAI - ' . strtoupper($jurusan['nama_jurusan']));
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
            $sheet->setCellValue('E' . $row, $item['mata_pelajaran']);
            
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
            
            $row++;
            $no++;
        }

        // Auto size columns
        $totalCols = 5 + $maxTugas + $maxUlangan; // 5 basic + tugas + ulangan
        for ($i = 0; $i < $totalCols; $i++) {
            $sheet->getColumnDimension(chr(65 + $i))->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Nilai_' . $jurusan['nama_jurusan'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Print rekap nilai per mata pelajaran
    public function print($jadwalId = null)
    {
        if (!$jadwalId) {
            return redirect()->to('admin/nilai')->with('error', 'Jadwal tidak ditemukan');
        }

        // Get jadwal info
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal j')
                    ->select('j.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, jur.nama_jurusan, u.full_name as nama_guru')
                    ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                    ->join('kelas k', 'k.id = j.kelas_id')
                    ->join('jurusan jur', 'jur.id = k.jurusan_id')
                    ->join('guru g', 'g.id = j.guru_id')
                    ->join('users u', 'u.id = g.user_id')
                    ->where('j.id', $jadwalId)
                    ->get()
                    ->getRowArray();

        if (!$jadwal) {
            return redirect()->to('admin/nilai')->with('error', 'Jadwal tidak ditemukan');
        }

        // Get siswa dan nilai
        $siswa = $this->nilaiModel->getSiswaByJadwal($jadwalId);
        $nilaiExisting = $this->nilaiModel->getNilaiByJadwal($jadwalId);
        
        // Format nilai per siswa
        $nilaiFormatted = [];
        $maxTugas = 0;
        $maxUlangan = 0;
        
        foreach ($nilaiExisting as $nilai) {
            $tugas = json_decode($nilai['nilai_tugas'], true) ?: [];
            $ulangan = json_decode($nilai['nilai_ulangan'], true) ?: [];
            
            $nilaiFormatted[$nilai['siswa_id']] = [
                'tugas' => $tugas,
                'ulangan' => $ulangan,
                'uts_sem1' => $nilai['nilai_uts_sem1'] ?? null,
                'uas_sem1' => $nilai['nilai_uas_sem1'] ?? null,
                'uts_sem2' => $nilai['nilai_uts_sem2'] ?? null,
                'uas_sem2' => $nilai['nilai_uas_sem2'] ?? null
            ];
            
            $maxTugas = max($maxTugas, count($tugas));
            $maxUlangan = max($maxUlangan, count($ulangan));
        }

        $data = [
            'title' => 'Rekap Nilai - ' . $jadwal['nama_mata_pelajaran'],
            'jadwal' => $jadwal,
            'siswa' => $siswa,
            'nilai' => $nilaiFormatted,
            'maxTugas' => $maxTugas,
            'maxUlangan' => $maxUlangan
        ];

        return view('admin/nilai/print', $data);
    }
} 