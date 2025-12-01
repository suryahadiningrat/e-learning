<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\SiswaModel;

class Nilai extends BaseController
{
    protected $nilaiModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->siswaModel = new SiswaModel();
    }

    // Tampilkan nilai siswa
    public function index()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get siswa berdasarkan user_id dengan relasi user
        $db = \Config\Database::connect();
        $siswa = $db->table('siswa s')
                   ->select('s.*, u.username, u.full_name, u.email, u.is_active, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, j.nama_jurusan')
                   ->join('users u', 'u.id = s.user_id')
                   ->join('kelas k', 'k.id = s.kelas_id')
                   ->join('jurusan j', 'j.id = k.jurusan_id')
                   ->where('s.user_id', $userId)
                   ->get()
                   ->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan. Silakan hubungi administrator.');
        }

        // Pastikan field yang diperlukan ada
        if (!isset($siswa['full_name']) || !isset($siswa['nis'])) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak lengkap. Silakan hubungi administrator.');
        }

        // Debug: Log data siswa untuk memastikan struktur
        log_message('debug', 'Siswa data: ' . json_encode($siswa));

        // Get semua nilai siswa
        $nilai = $this->nilaiModel->getNilaiBySiswa($siswa['id']);

        $data = [
            'title' => 'Data Nilai',
            'siswa' => $siswa,
            'nilai' => $nilai
        ];

        return view('siswa/nilai/index', $data);
    }

    // Export nilai siswa ke Excel
    public function export()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get siswa berdasarkan user_id dengan relasi user
        $db = \Config\Database::connect();
        $siswa = $db->table('siswa s')
                   ->select('s.*, u.username, u.full_name, u.email, u.is_active, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, j.nama_jurusan')
                   ->join('users u', 'u.id = s.user_id')
                   ->join('kelas k', 'k.id = s.kelas_id')
                   ->join('jurusan j', 'j.id = k.jurusan_id')
                   ->where('s.user_id', $userId)
                   ->get()
                   ->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/nilai')->with('error', 'Data siswa tidak ditemukan. Silakan hubungi administrator.');
        }

        // Pastikan field yang diperlukan ada
        if (!isset($siswa['full_name']) || !isset($siswa['nis'])) {
            return redirect()->to('siswa/nilai')->with('error', 'Data siswa tidak lengkap. Silakan hubungi administrator.');
        }

        // Debug: Log data siswa untuk memastikan struktur
        log_message('debug', 'Siswa data for export: ' . json_encode($siswa));

        // Get semua nilai siswa
        $nilai = $this->nilaiModel->getNilaiBySiswa($siswa['id']);

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
        $sheet->setCellValue('A1', 'DATA NILAI SISWA - ' . strtoupper($siswa['full_name']));
        $sheet->mergeCells('A1:' . chr(65 + 3 + $maxTugas + $maxUlangan) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Set headers
        $headers = ['No', 'Mata Pelajaran', 'Kelas'];
        
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
            $sheet->setCellValue('B' . $row, $item['nama_mata_pelajaran']);
            $sheet->setCellValue('C' . $row, $item['nama_kelas']);
            
            $col = 'D'; // Start from D (after kelas)
            
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
        $totalCols = 3 + $maxTugas + $maxUlangan; // 3 basic + tugas + ulangan
        for ($i = 0; $i < $totalCols; $i++) {
            $sheet->getColumnDimension(chr(65 + $i))->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Nilai_Siswa_' . $siswa['full_name'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Export nilai siswa ke PDF
    public function exportPdf()
    {
        $userId = session('user_id');
        
        if (!$userId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get siswa berdasarkan user_id dengan relasi user
        $db = \Config\Database::connect();
        $siswa = $db->table('siswa s')
                   ->select('s.*, u.username, u.full_name, u.email, u.is_active, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, j.nama_jurusan')
                   ->join('users u', 'u.id = s.user_id')
                   ->join('kelas k', 'k.id = s.kelas_id')
                   ->join('jurusan j', 'j.id = k.jurusan_id')
                   ->where('s.user_id', $userId)
                   ->get()
                   ->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/nilai')->with('error', 'Data siswa tidak ditemukan. Silakan hubungi administrator.');
        }

        // Get semua nilai siswa
        $nilai = $this->nilaiModel->getNilaiBySiswa($siswa['id']);

        // Hitung jumlah maksimal tugas dan ulangan
        $maxTugas = 0;
        $maxUlangan = 0;
        foreach ($nilai as $item) {
            $tugasArray = json_decode($item['nilai_tugas'], true) ?: [];
            $ulanganArray = json_decode($item['nilai_ulangan'], true) ?: [];
            $maxTugas = max($maxTugas, count($tugasArray));
            $maxUlangan = max($maxUlangan, count($ulanganArray));
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
                    font-size: 12px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header h1 {
                    font-size: 18px;
                    margin: 0;
                    padding: 10px 0;
                }
                .header h2 {
                    font-size: 14px;
                    margin: 0;
                    padding: 5px 0;
                    font-weight: normal;
                }
                .info-siswa {
                    margin-bottom: 15px;
                }
                .info-siswa table {
                    width: 100%;
                }
                .info-siswa td {
                    padding: 3px 5px;
                }
                .info-siswa td:first-child {
                    width: 120px;
                }
                table.nilai {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                table.nilai th, table.nilai td {
                    border: 1px solid #333;
                    padding: 6px 4px;
                    text-align: center;
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
                .footer p {
                    margin: 5px 0;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN NILAI SISWA</h1>
                <h2>Sistem Pembelajaran E-Learning</h2>
            </div>
            
            <div class="info-siswa">
                <table>
                    <tr>
                        <td><strong>Nama Siswa</strong></td>
                        <td>: ' . esc($siswa['full_name']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>NIS</strong></td>
                        <td>: ' . esc($siswa['nis']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Kelas</strong></td>
                        <td>: ' . esc($siswa['nama_kelas']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Jurusan</strong></td>
                        <td>: ' . esc($siswa['nama_jurusan']) . '</td>
                    </tr>
                </table>
            </div>
            
            <table class="nilai">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Mata Pelajaran</th>';
        
        if ($maxTugas > 0) {
            $html .= '<th colspan="' . $maxTugas . '">Nilai Tugas</th>';
        }
        if ($maxUlangan > 0) {
            $html .= '<th colspan="' . $maxUlangan . '">Nilai Ulangan</th>';
        }
        $html .= '<th rowspan="2">Rata-rata</th>
                    </tr>
                    <tr>';
        
        for ($i = 1; $i <= $maxTugas; $i++) {
            $html .= '<th>T' . $i . '</th>';
        }
        for ($i = 1; $i <= $maxUlangan; $i++) {
            $html .= '<th>U' . $i . '</th>';
        }
        $html .= '</tr>
                </thead>
                <tbody>';
        
        $no = 1;
        $totalRataRata = 0;
        $countNilai = 0;
        
        foreach ($nilai as $item) {
            $html .= '<tr>
                        <td>' . $no . '</td>
                        <td style="text-align: left;">' . esc($item['nama_mata_pelajaran']) . '</td>';
            
            $nilaiTugas = json_decode($item['nilai_tugas'], true) ?: [];
            $nilaiUlangan = json_decode($item['nilai_ulangan'], true) ?: [];
            
            // Nilai tugas
            $sumNilai = 0;
            $countValues = 0;
            for ($i = 0; $i < $maxTugas; $i++) {
                $val = isset($nilaiTugas[$i]) && $nilaiTugas[$i] !== '' ? $nilaiTugas[$i] : '-';
                $html .= '<td>' . $val . '</td>';
                if ($val !== '-' && is_numeric($val)) {
                    $sumNilai += floatval($val);
                    $countValues++;
                }
            }
            
            // Nilai ulangan
            for ($i = 0; $i < $maxUlangan; $i++) {
                $val = isset($nilaiUlangan[$i]) && $nilaiUlangan[$i] !== '' ? $nilaiUlangan[$i] : '-';
                $html .= '<td>' . $val . '</td>';
                if ($val !== '-' && is_numeric($val)) {
                    $sumNilai += floatval($val);
                    $countValues++;
                }
            }
            
            // Rata-rata per mata pelajaran
            $rataRata = $countValues > 0 ? round($sumNilai / $countValues, 2) : '-';
            if ($rataRata !== '-') {
                $totalRataRata += $rataRata;
                $countNilai++;
            }
            
            $html .= '<td><strong>' . $rataRata . '</strong></td>
                    </tr>';
            $no++;
        }
        
        // Rata-rata keseluruhan
        $rataRataKeseluruhan = $countNilai > 0 ? round($totalRataRata / $countNilai, 2) : '-';
        
        $html .= '</tbody>
                <tfoot>
                    <tr>
                        <td colspan="' . (2 + $maxTugas + $maxUlangan) . '" style="text-align: right;"><strong>Rata-rata Keseluruhan</strong></td>
                        <td><strong>' . $rataRataKeseluruhan . '</strong></td>
                    </tr>
                </tfoot>
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

        $filename = 'Rekap_Nilai_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $siswa['full_name']) . '_' . date('Y-m-d') . '.pdf';
        
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
} 