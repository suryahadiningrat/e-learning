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
                'ulangan' => json_decode($nilai['nilai_ulangan'], true) ?: []
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
        $filename = 'Nilai_Guru_' . $jurusan['nama_jurusan'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Export nilai ke PDF untuk guru
    public function exportPdf($jadwalId = null)
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
                    ->where('j.guru_id', $guruId)
                    ->get()
                    ->getRowArray();

        if (!$jadwal) {
            return redirect()->to('guru/nilai')->with('error', 'Jadwal tidak ditemukan atau Anda tidak berhak mengakses');
        }

        // Get guru info
        $guru = $db->table('guru g')
                   ->select('g.*, u.full_name as nama_guru')
                   ->join('users u', 'u.id = g.user_id')
                   ->where('g.id', $guruId)
                   ->get()
                   ->getRowArray();

        // Get siswa berdasarkan kelas di jadwal
        $siswa = $this->nilaiModel->getSiswaByJadwal($jadwalId);

        // Get nilai yang sudah ada
        $nilaiExisting = $this->nilaiModel->getNilaiByJadwal($jadwalId);
        $nilaiFormatted = [];
        
        foreach ($nilaiExisting as $nilai) {
            $nilaiFormatted[$nilai['siswa_id']] = [
                'tugas' => json_decode($nilai['nilai_tugas'], true) ?: [],
                'ulangan' => json_decode($nilai['nilai_ulangan'], true) ?: [],
                'uts_sem1' => $nilai['uts_sem1'] ?? '',
                'uas_sem1' => $nilai['uas_sem1'] ?? '',
                'uts_sem2' => $nilai['uts_sem2'] ?? '',
                'uas_sem2' => $nilai['uas_sem2'] ?? ''
            ];
        }

        // Hitung jumlah maksimal tugas dan ulangan
        $maxTugas = 2;
        $maxUlangan = 2;
        foreach ($nilaiFormatted as $nilai) {
            $maxTugas = max($maxTugas, count($nilai['tugas']));
            $maxUlangan = max($maxUlangan, count($nilai['ulangan']));
        }

        // Generate HTML for PDF
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header h1 {
                    font-size: 16px;
                    margin: 0;
                    padding: 10px 0;
                }
                .header h2 {
                    font-size: 13px;
                    margin: 0;
                    padding: 5px 0;
                    font-weight: normal;
                }
                .info {
                    margin-bottom: 15px;
                }
                .info table {
                    width: 100%;
                }
                .info td {
                    padding: 3px 5px;
                }
                .info td:first-child {
                    width: 120px;
                }
                table.nilai {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                table.nilai th, table.nilai td {
                    border: 1px solid #333;
                    padding: 4px 3px;
                    text-align: center;
                    font-size: 9px;
                }
                table.nilai th {
                    background-color: #343a40;
                    color: white;
                    font-weight: bold;
                }
                table.nilai tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .footer {
                    margin-top: 30px;
                    text-align: right;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>REKAP NILAI SISWA</h1>
                <h2>Sistem Pembelajaran E-Learning</h2>
            </div>
            
            <div class="info">
                <table>
                    <tr>
                        <td><strong>Guru</strong></td>
                        <td>: ' . esc($guru['nama_guru'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Mata Pelajaran</strong></td>
                        <td>: ' . esc($jadwal['nama_mata_pelajaran']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Kelas</strong></td>
                        <td>: ' . esc($jadwal['nama_kelas']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Jurusan</strong></td>
                        <td>: ' . esc($jadwal['nama_jurusan']) . '</td>
                    </tr>
                </table>
            </div>
            
            <table class="nilai">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">NIS</th>
                        <th rowspan="2">Nama Siswa</th>';
        
        // Tugas headers
        if ($maxTugas > 0) {
            $html .= '<th colspan="' . $maxTugas . '">Nilai Tugas</th>';
        }
        // Ulangan headers
        if ($maxUlangan > 0) {
            $html .= '<th colspan="' . $maxUlangan . '">Nilai Ulangan</th>';
        }
        
        $html .= '<th colspan="2">Semester 1</th>
                  <th colspan="2">Semester 2</th>
                  <th rowspan="2">Rata-rata</th>
                    </tr>
                    <tr>';
        
        for ($i = 1; $i <= $maxTugas; $i++) {
            $html .= '<th>T' . $i . '</th>';
        }
        for ($i = 1; $i <= $maxUlangan; $i++) {
            $html .= '<th>U' . $i . '</th>';
        }
        $html .= '<th>UTS</th><th>UAS</th><th>UTS</th><th>UAS</th>';
        $html .= '</tr>
                </thead>
                <tbody>';
        
        $no = 1;
        foreach ($siswa as $s) {
            $siswaId = $s['id'];
            $nilaiSiswa = $nilaiFormatted[$siswaId] ?? [
                'tugas' => [],
                'ulangan' => [],
                'uts_sem1' => '',
                'uas_sem1' => '',
                'uts_sem2' => '',
                'uas_sem2' => ''
            ];
            
            $html .= '<tr>
                        <td>' . $no++ . '</td>
                        <td>' . esc($s['nis']) . '</td>
                        <td style="text-align: left;">' . esc($s['nama_siswa']) . '</td>';
            
            // Nilai tugas
            $sumNilai = 0;
            $countValues = 0;
            for ($i = 0; $i < $maxTugas; $i++) {
                $val = isset($nilaiSiswa['tugas'][$i]) && $nilaiSiswa['tugas'][$i] !== '' ? $nilaiSiswa['tugas'][$i] : '-';
                $html .= '<td>' . $val . '</td>';
                if ($val !== '-' && is_numeric($val)) {
                    $sumNilai += floatval($val);
                    $countValues++;
                }
            }
            
            // Nilai ulangan
            for ($i = 0; $i < $maxUlangan; $i++) {
                $val = isset($nilaiSiswa['ulangan'][$i]) && $nilaiSiswa['ulangan'][$i] !== '' ? $nilaiSiswa['ulangan'][$i] : '-';
                $html .= '<td>' . $val . '</td>';
                if ($val !== '-' && is_numeric($val)) {
                    $sumNilai += floatval($val);
                    $countValues++;
                }
            }
            
            // UTS/UAS Semester 1
            $utsSem1 = !empty($nilaiSiswa['uts_sem1']) ? $nilaiSiswa['uts_sem1'] : '-';
            $uasSem1 = !empty($nilaiSiswa['uas_sem1']) ? $nilaiSiswa['uas_sem1'] : '-';
            $html .= '<td>' . $utsSem1 . '</td>';
            $html .= '<td>' . $uasSem1 . '</td>';
            if ($utsSem1 !== '-' && is_numeric($utsSem1)) {
                $sumNilai += floatval($utsSem1);
                $countValues++;
            }
            if ($uasSem1 !== '-' && is_numeric($uasSem1)) {
                $sumNilai += floatval($uasSem1);
                $countValues++;
            }
            
            // UTS/UAS Semester 2
            $utsSem2 = !empty($nilaiSiswa['uts_sem2']) ? $nilaiSiswa['uts_sem2'] : '-';
            $uasSem2 = !empty($nilaiSiswa['uas_sem2']) ? $nilaiSiswa['uas_sem2'] : '-';
            $html .= '<td>' . $utsSem2 . '</td>';
            $html .= '<td>' . $uasSem2 . '</td>';
            if ($utsSem2 !== '-' && is_numeric($utsSem2)) {
                $sumNilai += floatval($utsSem2);
                $countValues++;
            }
            if ($uasSem2 !== '-' && is_numeric($uasSem2)) {
                $sumNilai += floatval($uasSem2);
                $countValues++;
            }
            
            // Rata-rata
            $rataRata = $countValues > 0 ? round($sumNilai / $countValues, 2) : '-';
            $html .= '<td><strong>' . $rataRata . '</strong></td>
                    </tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="footer">
                <p>Dicetak pada: ' . date('d F Y, H:i:s') . '</p>
            </div>
        </body>
        </html>';

        // Create PDF using DOMPDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Rekap_Nilai_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $jadwal['nama_mata_pelajaran']) . '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $jadwal['nama_kelas']) . '_' . date('Y-m-d') . '.pdf';
        
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
} 