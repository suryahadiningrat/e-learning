<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;

class Siswa extends BaseController {
    protected $siswaModel;
    protected $kelasModel;
    protected $jurusanModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
    }

    public function index() {
        $data = [
            'title' => 'Data Siswa',
            'jurusan' => $this->jurusanModel->findAll()
        ];

        return view('guru/siswa/index', $data);
    }

    public function jurusan($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('guru/siswa')->with('error', 'Jurusan tidak ditemukan');
        }

        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->to('guru/siswa')->with('error', 'Jurusan tidak ditemukan');
        }

        $kelas = $this->kelasModel->getKelasByJurusan($jurusanId);

        $data = [
            'title' => 'Kelas - ' . $jurusan['nama_jurusan'],
            'jurusan' => $jurusan,
            'kelas' => $kelas
        ];

        return view('guru/siswa/kelas', $data);
    }

    public function kelas($kelasId = null)
    {
        if (!$kelasId) {
            return redirect()->to('guru/siswa')->with('error', 'Kelas tidak ditemukan');
        }

        $kelas = $this->kelasModel->getKelasWithRelations($kelasId);
        if (!$kelas) {
            return redirect()->to('guru/siswa')->with('error', 'Kelas tidak ditemukan');
        }

        $siswa = $this->siswaModel->getSiswaByKelas($kelasId);

        $data = [
            'title' => 'Siswa - ' . $kelas['nama_kelas'],
            'kelas' => $kelas,
            'siswa' => $siswa
        ];

        return view('guru/siswa/siswa', $data);
    }

    // Export siswa by jurusan
    public function exportJurusan($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('guru/siswa')->with('error', 'Jurusan tidak ditemukan');
        }

        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->to('guru/siswa')->with('error', 'Jurusan tidak ditemukan');
        }

        $siswa = $this->siswaModel->getSiswaByJurusan($jurusanId);

        // Export to Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'DATA SISWA - ' . strtoupper($jurusan['nama_jurusan']));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Set headers
        $headers = ['No', 'NIS', 'Nama Siswa', 'Jenis Kelamin', 'Tempat/Tanggal Lahir', 'Alamat', 'No. Telepon', 'Kelas'];
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
        foreach ($siswa as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['nis']);
            $sheet->setCellValue('C' . $row, $item['full_name']);
            $sheet->setCellValue('D' . $row, $item['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $item['tempat_lahir'] . ', ' . date('d/m/Y', strtotime($item['tanggal_lahir'])));
            $sheet->setCellValue('F' . $row, $item['alamat']);
            $sheet->setCellValue('G' . $row, $item['no_telp']);
            $sheet->setCellValue('H' . $row, $item['nama_kelas']);
            
            $row++;
            $no++;
        }

        // Auto size columns
        for ($i = 0; $i < 8; $i++) {
            $sheet->getColumnDimension(chr(65 + $i))->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Siswa_' . $jurusan['nama_jurusan'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Export siswa by kelas
    public function exportKelas($kelasId = null)
    {
        if (!$kelasId) {
            return redirect()->to('guru/siswa')->with('error', 'Kelas tidak ditemukan');
        }

        $kelas = $this->kelasModel->getKelasWithRelations($kelasId);
        if (!$kelas) {
            return redirect()->to('guru/siswa')->with('error', 'Kelas tidak ditemukan');
        }

        $siswa = $this->siswaModel->getSiswaByKelas($kelasId);

        // Export to Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'DATA SISWA - ' . strtoupper($kelas['nama_kelas']));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Set headers
        $headers = ['No', 'NIS', 'Nama Siswa', 'Jenis Kelamin', 'Tempat/Tanggal Lahir', 'Alamat', 'No. Telepon', 'Email'];
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
        foreach ($siswa as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['nis']);
            $sheet->setCellValue('C' . $row, $item['full_name']);
            $sheet->setCellValue('D' . $row, $item['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $item['tempat_lahir'] . ', ' . date('d/m/Y', strtotime($item['tanggal_lahir'])));
            $sheet->setCellValue('F' . $row, $item['alamat']);
            $sheet->setCellValue('G' . $row, $item['no_telp']);
            $sheet->setCellValue('H' . $row, $item['email']);
            
            $row++;
            $no++;
        }

        // Auto size columns
        for ($i = 0; $i < 8; $i++) {
            $sheet->getColumnDimension(chr(65 + $i))->setAutoSize(true);
        }

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Siswa_' . str_replace(' ', '_', $kelas['nama_kelas']) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
} 