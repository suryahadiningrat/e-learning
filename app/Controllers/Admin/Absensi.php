<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JadwalModel;

class Absensi extends BaseController
{
    protected $absensiModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jadwalModel = new JadwalModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kelasId = $this->request->getGet('kelas_id');

        $data = [
            'title' => 'Data Absensi',
            'absensi' => $this->absensiModel->getAbsensiWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'kelas_id' => $kelasId,
            ]
        ];

        return view('admin/absensi/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Absensi',
            'siswa' => $this->siswaModel->getSiswaWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];

        return view('admin/absensi/create', $data);
    }

    public function store()
    {
        // Validasi input dengan pengecekan manual untuk duplikasi absensi
        $siswaId = $this->request->getPost('siswa_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        
        // Cek apakah sudah ada absensi untuk siswa, jadwal, dan tanggal yang sama
        $existingAbsensi = $this->absensiModel->checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal);
        if ($existingAbsensi) {
            return redirect()->back()->withInput()->with('error', 'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada');
        }

        // Validasi input lainnya
        $rules = [
            'siswa_id' => 'required|numeric',
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
            'keterangan' => 'permit_empty|max_length[255]'
        ];

        $messages = [
            'siswa_id' => [
                'required' => 'Siswa harus dipilih',
                'numeric' => 'Siswa tidak valid'
            ],
            'jadwal_id' => [
                'required' => 'Jadwal harus dipilih',
                'numeric' => 'Jadwal tidak valid'
            ],
            'tanggal' => [
                'required' => 'Tanggal harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ],
            'status' => [
                'required' => 'Status absensi harus dipilih',
                'in_list' => 'Status absensi tidak valid'
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 255 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Buat data absensi
            $absensiData = [
                'siswa_id' => $siswaId,
                'jadwal_id' => $jadwalId,
                'tanggal' => $tanggal,
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('absensi')->insert($absensiData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan absensi');
            }

            return redirect()->to('admin/absensi')->with('success', 'Absensi berhasil ditambahkan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan absensi: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $absensi = $this->absensiModel->getAbsensiWithRelations($id);
        
        if (!$absensi) {
            return redirect()->to('admin/absensi')->with('error', 'Absensi tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Absensi',
            'absensi' => $absensi,
            'siswa' => $this->siswaModel->getSiswaWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];

        return view('admin/absensi/edit', $data);
    }

    public function update($id = null)
    {
        $absensi = $this->absensiModel->find($id);
        
        if (!$absensi) {
            return redirect()->to('admin/absensi')->with('error', 'Absensi tidak ditemukan');
        }

        // Validasi input dengan pengecekan manual untuk duplikasi absensi
        $siswaId = $this->request->getPost('siswa_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        
        // Cek apakah sudah ada absensi untuk siswa, jadwal, dan tanggal yang sama (kecuali yang sedang diedit)
        $existingAbsensi = $this->absensiModel->checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal, $id);
        if ($existingAbsensi) {
            return redirect()->back()->withInput()->with('error', 'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada');
        }

        // Validasi input lainnya
        $rules = [
            'siswa_id' => 'required|numeric',
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
            'keterangan' => 'permit_empty|max_length[255]'
        ];

        $messages = [
            'siswa_id' => [
                'required' => 'Siswa harus dipilih',
                'numeric' => 'Siswa tidak valid'
            ],
            'jadwal_id' => [
                'required' => 'Jadwal harus dipilih',
                'numeric' => 'Jadwal tidak valid'
            ],
            'tanggal' => [
                'required' => 'Tanggal harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ],
            'status' => [
                'required' => 'Status absensi harus dipilih',
                'in_list' => 'Status absensi tidak valid'
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 255 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update data absensi
            $absensiData = [
                'siswa_id' => $siswaId,
                'jadwal_id' => $jadwalId,
                'tanggal' => $tanggal,
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('absensi')->where('id', $id)->update($absensiData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui absensi');
            }

            return redirect()->to('admin/absensi')->with('success', 'Absensi berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui absensi: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $absensi = $this->absensiModel->find($id);
        
        if (!$absensi) {
            return redirect()->to('admin/absensi')->with('error', 'Absensi tidak ditemukan');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus data absensi
            $db->table('absensi')->where('id', $id)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('admin/absensi')->with('error', 'Gagal menghapus absensi');
            }

            return redirect()->to('admin/absensi')->with('success', 'Absensi berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/absensi')->with('error', 'Gagal menghapus absensi: ' . $e->getMessage());
        }
    }

    public function getJadwalByKelas($kelasId)
    {
        $jadwal = $this->jadwalModel->getJadwalByKelas($kelasId);
        return $this->response->setJSON($jadwal);
    }

    public function getSiswaByKelas($kelasId)
    {
        $siswa = $this->siswaModel->getSiswaByKelas($kelasId);
        return $this->response->setJSON($siswa);
    }

    public function export()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kelasId = $this->request->getGet('kelas_id');

        // Get data absensi dengan filter
        $absensi = $this->absensiModel->getAbsensiForExport($startDate, $endDate, $kelasId);

        // Load PhpSpreadsheet
        require_once ROOTPATH . 'vendor/autoload.php';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul
        $sheet->setCellValue('A1', 'LAPORAN ABSENSI SISWA');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set periode
        $periode = '';
        if ($startDate && $endDate) {
            $periode = 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate));
        } elseif ($startDate) {
            $periode = 'Tanggal: ' . date('d/m/Y', strtotime($startDate));
        }
        if ($periode) {
            $sheet->setCellValue('A2', $periode);
            $sheet->mergeCells('A2:K2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Header tabel
        $headers = [
            'No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 
            'Guru', 'Hari', 'Jam', 'Status', 'Keterangan'
        ];

        $col = 'A';
        $row = 4;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E2EFDA');
            $col++;
        }

        // Data absensi
        $no = 1;
        $row = 5;
        foreach ($absensi as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('C' . $row, $item['nis']);
            $sheet->setCellValue('D' . $row, $item['nama_siswa']);
            $sheet->setCellValue('E' . $row, $item['nama_kelas'] . ' - ' . $item['nama_jurusan']);
            $sheet->setCellValue('F' . $row, $item['mata_pelajaran']);
            $sheet->setCellValue('G' . $row, $item['nama_guru']);
            $sheet->setCellValue('H' . $row, $item['hari']);
            $sheet->setCellValue('I' . $row, $item['jam_mulai'] . ' - ' . $item['jam_selesai']);
            $sheet->setCellValue('J' . $row, $item['status']);
            $sheet->setCellValue('K' . $row, $item['keterangan'] ?? '-');

            // Set warna status
            $statusColor = '';
            switch ($item['status']) {
                case 'Hadir':
                    $statusColor = '28A745'; // Hijau
                    break;
                case 'Sakit':
                    $statusColor = 'FFC107'; // Kuning
                    break;
                case 'Izin':
                    $statusColor = '17A2B8'; // Biru
                    break;
                case 'Alpha':
                    $statusColor = 'DC3545'; // Merah
                    break;
            }
            if ($statusColor) {
                $sheet->getStyle('J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($statusColor);
            }

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set border
        $lastRow = $row - 1;
        $sheet->getStyle('A4:K' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Set filename
        $filename = 'Laporan_Absensi_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
} 