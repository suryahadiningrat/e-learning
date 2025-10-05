<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JadwalModel;
use App\Models\JurusanModel;
use App\Models\HariAbsensiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Absensi extends BaseController
{
    protected $absensiModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $jadwalModel;
    protected $jurusanModel;
    protected $hariAbsensiModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jadwalModel = new JadwalModel();
        $this->jurusanModel = new JurusanModel();
        $this->hariAbsensiModel = new HariAbsensiModel();
    }

    // Structured navigation flow for admin (with full access)
    public function index()
    {
        $data = [
            'title' => 'Data Absensi - Pilih Jurusan',
            'jurusan' => $this->jurusanModel->findAll()
        ];

        return view('admin/absensi/index', $data);
    }

    public function kelas($jurusanId)
    {
        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->to('admin/absensi')->with('error', 'Jurusan tidak ditemukan');
        }

        $kelas = $this->kelasModel->where('jurusan_id', $jurusanId)->findAll();

        $data = [
            'title' => 'Data Absensi - Pilih Kelas',
            'jurusan' => $jurusan,
            'kelas' => $kelas
        ];

        return view('admin/absensi/kelas', $data);
    }

    public function jadwal($kelasId)
    {
        $kelas = $this->kelasModel->getKelasWithRelations($kelasId);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        $jadwal = $this->jadwalModel->getJadwalByKelas($kelasId);

        $data = [
            'title' => 'Data Absensi - Pilih Jadwal',
            'kelas' => $kelas,
            'jadwal' => $jadwal
        ];

        return view('admin/absensi/jadwal', $data);
    }

    public function hari($jadwalId)
    {
        $jadwal = $this->jadwalModel->getJadwalWithRelations($jadwalId);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        $hariAbsensi = $this->hariAbsensiModel->getHariAbsensiByJadwal($jadwalId);

        $data = [
            'title' => 'Data Absensi - Pilih Hari',
            'jadwal' => $jadwal,
            'hari_absensi' => $hariAbsensi
        ];

        return view('admin/absensi/hari', $data);
    }

    public function input($hariAbsensiId)
    {
        $hariAbsensi = $this->hariAbsensiModel->getHariAbsensiWithRelations($hariAbsensiId);
        if (!$hariAbsensi) {
            return redirect()->back()->with('error', 'Hari absensi tidak ditemukan');
        }

        $siswa = $this->siswaModel->getSiswaByJadwal($hariAbsensi['jadwal_id']);
        $absensi = $this->absensiModel->getAbsensiByHariAbsensi($hariAbsensiId);

        $data = [
            'title' => 'Input Absensi',
            'hari_absensi' => $hariAbsensi,
            'jadwal' => $hariAbsensi,
            'siswa' => $siswa,
            'absensi' => $absensi
        ];

        return view('admin/absensi/input', $data);
    }

    public function createHari($jadwalId)
    {
        $jadwal = $this->jadwalModel->getJadwalWithRelations($jadwalId);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        $data = [
            'title' => 'Buat Hari Absensi Baru',
            'jadwal' => $jadwal
        ];

        return view('admin/absensi/create_hari', $data);
    }

    public function storeHari()
    {
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');

        // Validasi
        if (!$jadwalId || !$tanggal) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        // Cek apakah hari absensi sudah ada
        $existing = $this->hariAbsensiModel->where([
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal
        ])->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Hari absensi untuk tanggal ini sudah ada');
        }

        // Simpan hari absensi baru
        $data = [
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->hariAbsensiModel->save($data)) {
            $hariAbsensiId = $this->hariAbsensiModel->getInsertID();
            return redirect()->to("admin/absensi/input/{$hariAbsensiId}")->with('success', 'Hari absensi berhasil dibuat');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat hari absensi');
        }
    }

    // Export functions with full admin access
    public function exportHari($hariAbsensiId)
    {
        $hariAbsensi = $this->hariAbsensiModel->getHariAbsensiWithRelations($hariAbsensiId);
        if (!$hariAbsensi) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Get data absensi untuk hari ini
        $db = \Config\Database::connect();
        $absensi = $db->table('absensi a')
                     ->select('a.*, s.nama as nama_siswa, s.nis')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->where('a.hari_absensi_id', $hariAbsensiId)
                     ->orderBy('s.nama')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Hari - ' . date('d/m/Y', strtotime($hariAbsensi['tanggal'])));
    }

    public function exportJadwal($jadwalId)
    {
        $jadwal = $this->jadwalModel->getJadwalWithRelations($jadwalId);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Get data absensi untuk jadwal ini
        $db = \Config\Database::connect();
        $absensi = $db->table('absensi a')
                     ->select('a.*, s.nama as nama_siswa, s.nis, ha.tanggal')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->where('ha.jadwal_id', $jadwalId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('s.nama')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Jadwal - ' . $jadwal['nama_mata_pelajaran'] . ' (' . $jadwal['nama_kelas'] . ')');
    }

    public function exportKelas($kelasId)
    {
        $kelas = $this->kelasModel->getKelasWithRelations($kelasId);
        if (!$kelas) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Get data absensi untuk semua jadwal di kelas ini
        $db = \Config\Database::connect();
        $absensi = $db->table('absensi a')
                     ->select('a.*, s.nama as nama_siswa, s.nis, ha.tanggal, mp.nama as nama_mata_pelajaran')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->where('j.kelas_id', $kelasId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('s.nama')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Kelas - ' . $kelas['nama_kelas']);
    }

    public function exportJurusan($jurusanId)
    {
        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Get data absensi untuk semua kelas di jurusan ini
        $db = \Config\Database::connect();
        $absensi = $db->table('absensi a')
                     ->select('a.*, s.nama as nama_siswa, s.nis, ha.tanggal, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->join('kelas k', 'k.id = j.kelas_id')
                     ->where('k.jurusan_id', $jurusanId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('s.nama')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Jurusan - ' . $jurusan['nama_jurusan']);
    }

    private function generateExcel($data, $title)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['No', 'NIS', 'Nama Siswa', 'Tanggal', 'Status', 'Keterangan'];
        if (isset($data[0]['nama_mata_pelajaran'])) {
            array_splice($headers, 3, 0, 'Mata Pelajaran');
        }
        if (isset($data[0]['nama_kelas'])) {
            array_splice($headers, 3, 0, 'Kelas');
        }

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Set data
        $row = 2;
        foreach ($data as $index => $item) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $index + 1);
            $sheet->setCellValue($col++ . $row, $item['nis']);
            $sheet->setCellValue($col++ . $row, $item['nama_siswa']);
            
            if (isset($item['nama_kelas'])) {
                $sheet->setCellValue($col++ . $row, $item['nama_kelas']);
            }
            if (isset($item['nama_mata_pelajaran'])) {
                $sheet->setCellValue($col++ . $row, $item['nama_mata_pelajaran']);
            }
            
            $sheet->setCellValue($col++ . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue($col++ . $row, $item['status']);
            $sheet->setCellValue($col++ . $row, $item['keterangan']);
            $row++;
        }

        // Auto size columns
        foreach (range('A', $col) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = $title . '_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Legacy methods for backward compatibility
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