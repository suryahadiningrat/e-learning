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
} 