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

        $this->db = \Config\Database::connect();
        $this->absensiModel = new AbsensiModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jadwalModel = new JadwalModel();
        $this->jurusanModel = new JurusanModel();
        $this->hariAbsensiModel = new HariAbsensiModel();
        
        // Validate admin session
        if (session('role') !== 'admin') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses tidak diizinkan');
        }
    }

    // Step 1: Tampilkan daftar jurusan (admin memiliki akses ke semua jurusan)
    public function index()
    {
        $jurusan = $this->jurusanModel->findAll();
        
        $data = [
            'title' => 'Data Presensi',
            'jurusan' => $jurusan
        ];

        return view('admin/absensi/index', $data);
    }

    // Step 2: Tampilkan kelas berdasarkan jurusan
    public function kelas($jurusanId = null)
    {
        if (!$jurusanId) {
            return redirect()->to('admin/absensi')->with('error', 'Jurusan tidak ditemukan');
        }

        $jurusan = $this->jurusanModel->find($jurusanId);
        if (!$jurusan) {
            return redirect()->to('admin/absensi')->with('error', 'Jurusan tidak ditemukan');
        }

        $kelas = $this->kelasModel->where('jurusan_id', $jurusanId)->findAll();

        $data = [
            'title' => 'Data Presensi - Kelas ' . $jurusan['nama_jurusan'],
            'jurusan' => $jurusan,
            'kelas' => $kelas
        ];

        return view('admin/absensi/kelas', $data);
    }

    // Step 3: Tampilkan jadwal berdasarkan kelas
    public function jadwal($kelasId = null)
    {
        if (!$kelasId) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        // Get kelas with relations using query builder
        $kelas = $this->db->table('kelas k')
            ->select('k.*, j.nama_jurusan, j.kode_jurusan, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->where('k.id', $kelasId)
            ->get()
            ->getRowArray();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        // Get jadwal for this kelas
        $jadwal = $this->db->table('jadwal jd')
            ->select('jd.*, mp.nama, u.full_name as nama_guru, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas, j.nama_jurusan')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->join('kelas k', 'k.id = jd.kelas_id')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->where('jd.kelas_id', $kelasId)
            ->orderBy('jd.hari, jd.jam_mulai')
            ->get()
            ->getResultArray();
            
        $data = [
            'title' => 'Data Presensi - Jadwal ' . $kelas['nama_kelas'],
            'kelas' => $kelas,
            'jadwal' => $jadwal
        ];

        return view('admin/absensi/jadwal', $data);
    }

    // Step 4: Tampilkan hari absensi berdasarkan jadwal
    public function hari($jadwalId = null)
    {
        if (!$jadwalId) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        // Get jadwal with relations using query builder
        $jadwal = $this->db->table('jadwal jd')
            ->select('jd.*, mp.nama, u.full_name as nama_guru, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas, j.nama_jurusan, j.kode_jurusan')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->join('kelas k', 'k.id = jd.kelas_id')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->where('jd.id', $jadwalId)
            ->get()
            ->getRowArray();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        // Get hari absensi for this jadwal
        $hariAbsensi = $this->getHariAbsensiByJadwal($jadwalId);

        $data = [
            'title' => 'Data Presensi - ' . $jadwal['nama'] . ' (' . $jadwal['nama_kelas'] . ')',
            'jadwal' => $jadwal,
            'hari_absensi' => $hariAbsensi
        ];

        return view('admin/absensi/hari', $data);
    }

    // Step 5: Input absensi untuk hari tertentu (sama dengan inputAbsensi di guru)
    public function input($hariAbsensiId = null)
    {
        if (!$hariAbsensiId) {
            return redirect()->back()->with('error', 'Hari absensi tidak ditemukan');
        }

        // Get hari absensi with relations using query builder
        $hariAbsensi = $this->db->table('hari_absensi ha')
            ->select('ha.*, jd.*, mp.nama, u.full_name as nama_guru, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas, j.nama_jurusan, j.kode_jurusan')
            ->join('jadwal jd', 'jd.id = ha.jadwal_id')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->join('kelas k', 'k.id = jd.kelas_id')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->where('ha.id', $hariAbsensiId)
            ->get()
            ->getRowArray();

        if (!$hariAbsensi) {
            return redirect()->back()->with('error', 'Hari absensi tidak ditemukan');
        }

        // Get siswa by jadwal
        $siswa = $this->getSiswaByJadwal($hariAbsensi['jadwal_id']);
        
        // Get existing absensi
        $absensi = $this->getAbsensiByHariAbsensi($hariAbsensiId);

        $data = [
            'title' => 'Input Presensi - ' . $hariAbsensi['nama'] . ' (' . date('d/m/Y', strtotime($hariAbsensi['tanggal'])) . ')',
            'hari_absensi' => $hariAbsensi,
            'hari_absensi_id' => $hariAbsensiId,
            'jadwal' => $hariAbsensi,
            'siswa' => $siswa,
            'absensi' => $absensi
        ];

        return view('admin/absensi/input', $data);
    }

    public function createHari($jadwalId = null)
    {
        if (!$jadwalId) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        // Get jadwal with relations using query builder
        $jadwal = $this->db->table('jadwal jd')
            ->select('jd.*, mp.nama, u.full_name as nama_guru, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas, j.nama_jurusan, j.kode_jurusan')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->join('kelas k', 'k.id = jd.kelas_id')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->where('jd.id', $jadwalId)
            ->get()
            ->getRowArray();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        $data = [
            'title' => 'Buat Hari Presensi Baru - ' . $jadwal['nama'],
            'jadwal' => $jadwal
        ];

        return view('admin/absensi/create_hari', $data);
    }

    public function storeHari()
    {
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        $status = $this->request->getPost('status') ?? 'aktif';

        // Validasi
        if (!$jadwalId || !$tanggal) {
            return redirect()->back()->with('error', 'Data tidak lengkap')->withInput();
        }

        // Cek apakah hari absensi sudah ada
        $existing = $this->db->table('hari_absensi')
            ->where([
                'jadwal_id' => $jadwalId,
                'tanggal' => $tanggal
            ])
            ->get()
            ->getRowArray();

        if ($existing) {
            return redirect()->back()->with('error', 'Hari absensi untuk tanggal ini sudah ada')->withInput();
        }

        // Simpan hari absensi baru
        $data = [
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $inserted = $this->db->table('hari_absensi')->insert($data);
        
        if ($inserted) {
            $hariAbsensiId = $this->db->insertID();
            return redirect()->to("admin/absensi/input/{$hariAbsensiId}")->with('success', 'Hari absensi berhasil dibuat');
        } else {
            return redirect()->back()->with('error', 'Gagal membuat hari absensi')->withInput();
        }
    }

    // Export functions with full admin access
    public function exportHari($hariAbsensiId)
    {
        // Get hari absensi with relations using query builder
        $hariAbsensi = $this->db->table('hari_absensi ha')
            ->select('ha.*, jd.*, mp.nama, u.full_name as nama_guru, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas, j.nama_jurusan, j.kode_jurusan')
            ->join('jadwal jd', 'jd.id = ha.jadwal_id')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->join('kelas k', 'k.id = jd.kelas_id')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->where('ha.id', $hariAbsensiId)
            ->get()
            ->getRowArray();

        if (!$hariAbsensi) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Get data absensi untuk hari ini
        $absensi = $this->db->table('absensi a')
            ->select('a.*, u.full_name as nama_siswa, s.nis')
            ->join('siswa s', 's.id = a.siswa_id')
            ->join('users u', 'u.id = s.user_id')
            ->where('a.hari_absensi_id', $hariAbsensiId)
            ->orderBy('u.full_name')
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
                     ->select('a.*, u.full_name as nama_siswa, s.nis, ha.tanggal')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('users u', 'u.id = s.user_id')
                     ->where('ha.jadwal_id', $jadwalId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('u.full_name')
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
                     ->select('a.*, u.full_name as nama_siswa, s.nis, ha.tanggal, mp.nama as nama_mata_pelajaran')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->join('users u', 'u.id = s.user_id')
                     ->where('j.kelas_id', $kelasId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('u.full_name')
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
                     ->select('a.*, u.full_name as nama_siswa, s.nis, ha.tanggal, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->join('kelas k', 'k.id = j.kelas_id')
                     ->join('users u', 'u.id = s.user_id')
                     ->where('k.jurusan_id', $jurusanId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('u.full_name')
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
            'title' => 'Tambah Presensi',
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
            'title' => 'Edit Presensi',
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
        $jadwal = $this->db->table('jadwal jd')
            ->select('jd.*, mp.nama, u.full_name as nama_guru')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->where('jd.kelas_id', $kelasId)
            ->get()
            ->getResultArray();
            
        return $this->response->setJSON($jadwal);
    }

    public function getSiswaByKelas($kelasId)
    {
        $siswa = $this->db->table('siswa s')
            ->select('s.*, u.full_name')
            ->join('users u', 'u.id = s.user_id')
            ->where('s.kelas_id', $kelasId)
            ->orderBy('u.full_name')
            ->get()
            ->getResultArray();
            
        return $this->response->setJSON($siswa);
    }

    // Helper methods untuk mendukung struktur navigasi baru
    private function getHariAbsensiByJadwal($jadwalId)
    {
        return $this->db->table('hari_absensi ha')
            ->select('ha.*, jd.*, mp.nama, u.full_name as nama_guru, ha.id AS id, CONCAT(k.kode_jurusan, k.tingkat) AS nama_kelas')
            ->join('jadwal jd', 'jd.id = ha.jadwal_id')
            ->join('mata_pelajaran mp', 'mp.id = jd.mata_pelajaran_id')
            ->join('guru g', 'g.id = jd.guru_id')
            ->join('users u', 'u.id = g.user_id')
            ->join('kelas k', 'k.id = jd.kelas_id')
            ->where('ha.jadwal_id', $jadwalId)
            ->orderBy('ha.tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function getSiswaByJadwal($jadwalId)
    {
        return $this->db->table('siswa s')
            ->select('s.*, u.full_name')
            ->join('users u', 'u.id = s.user_id')
            ->join('jadwal jd', 'jd.kelas_id = s.kelas_id')
            ->where('jd.id', $jadwalId)
            ->orderBy('u.full_name')
            ->get()
            ->getResultArray();
    }

    private function getAbsensiByHariAbsensi($hariAbsensiId)
    {
        return $this->db->table('absensi a')
            ->select('a.*, s.id as siswa_id, u.full_name as nama_siswa, s.nis')
            ->join('siswa s', 's.id = a.siswa_id')
            ->join('users u', 'u.id = s.user_id')
            ->where('a.hari_absensi_id', $hariAbsensiId)
            ->get()
            ->getResultArray();
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

    public function storeAbsensi()
    {
        $hariAbsensiId = $this->request->getPost('hari_absensi_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $siswaIds = $this->request->getPost('siswa_id');
        $statuses = $this->request->getPost('status');
        $keterangans = $this->request->getPost('keterangan');

        if (!$hariAbsensiId || !$jadwalId || !$siswaIds || !$statuses) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus absensi yang sudah ada untuk hari ini
            $db->table('absensi')
               ->where('hari_absensi_id', $hariAbsensiId)
               ->delete();

            // Simpan absensi baru
            for ($i = 0; $i < count($siswaIds); $i++) {
                $data = [
                    'siswa_id' => $siswaIds[$i],
                    'jadwal_id' => $jadwalId,
                    'hari_absensi_id' => $hariAbsensiId,
                    'status' => $statuses[$i],
                    'keterangan' => $keterangans[$i] ?? null,
                    'tanggal'    => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $db->table('absensi')->insert($data);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal menyimpan absensi');
            }

            return redirect()->to('admin/absensi/input/' . $hariAbsensiId)
                           ->with('success', 'Absensi berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Export rekap presensi ke PDF
    public function exportPdf()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kelasId = $this->request->getGet('kelas_id');
        $jurusanId = $this->request->getGet('jurusan_id');
        
        // Build query
        $builder = $this->db->table('absensi a')
            ->select('a.*, s.nis, u.full_name as nama_siswa, k.tingkat, k.kode_jurusan, k.paralel,
                      CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas,
                      j.nama_jurusan, ha.tanggal')
            ->join('siswa s', 's.id = a.siswa_id')
            ->join('users u', 'u.id = s.user_id')
            ->join('kelas k', 'k.id = s.kelas_id')
            ->join('jurusan j', 'j.id = k.jurusan_id')
            ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id');
        
        if ($startDate) {
            $builder->where('ha.tanggal >=', $startDate);
        }
        if ($endDate) {
            $builder->where('ha.tanggal <=', $endDate);
        }
        if ($kelasId) {
            $builder->where('k.id', $kelasId);
        }
        if ($jurusanId) {
            $builder->where('j.id', $jurusanId);
        }
        
        $absensiList = $builder->orderBy('ha.tanggal', 'DESC')
                               ->orderBy('nama_kelas', 'ASC')
                               ->orderBy('nama_siswa', 'ASC')
                               ->get()
                               ->getResultArray();
        
        // Get filter info
        $filterInfo = '';
        if ($kelasId) {
            $kelas = $this->kelasModel->find($kelasId);
            if ($kelas) {
                $filterInfo = $kelas['tingkat'] . ' ' . $kelas['kode_jurusan'] . ' ' . $kelas['paralel'];
            }
        } elseif ($jurusanId) {
            $jurusan = $this->jurusanModel->find($jurusanId);
            if ($jurusan) {
                $filterInfo = $jurusan['nama_jurusan'];
            }
        }
        
        // Group by date for summary
        $summaryByDate = [];
        foreach ($absensiList as $item) {
            $date = $item['tanggal'];
            if (!isset($summaryByDate[$date])) {
                $summaryByDate[$date] = [
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alfa' => 0,
                    'total' => 0
                ];
            }
            $status = strtolower($item['status']);
            if (isset($summaryByDate[$date][$status])) {
                $summaryByDate[$date][$status]++;
            }
            $summaryByDate[$date]['total']++;
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
                    font-size: 11px;
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
                table.data {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                table.data th, table.data td {
                    border: 1px solid #333;
                    padding: 5px 3px;
                    text-align: center;
                }
                table.data th {
                    background-color: #343a40;
                    color: white;
                    font-weight: bold;
                    font-size: 10px;
                }
                table.data td {
                    font-size: 9px;
                }
                .status-hadir { background-color: #d4edda; }
                .status-izin { background-color: #cce5ff; }
                .status-sakit { background-color: #fff3cd; }
                .status-alfa { background-color: #f8d7da; }
                .summary-table {
                    width: 50%;
                    margin: 20px auto;
                    border-collapse: collapse;
                }
                .summary-table th, .summary-table td {
                    border: 1px solid #333;
                    padding: 8px;
                    text-align: center;
                }
                .summary-table th {
                    background-color: #6c757d;
                    color: white;
                }
                .footer {
                    margin-top: 30px;
                    text-align: right;
                }
                .signature {
                    margin-top: 50px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>REKAP PRESENSI</h1>
                <h2>Sistem Informasi SMK Negeri 1 Pleret</h2>
            </div>
            
            <div class="info">
                <table>
                    <tr>
                        <td><strong>Periode</strong></td>
                        <td>: ' . ($startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua') . ' - ' . ($endDate ? date('d/m/Y', strtotime($endDate)) : 'Semua') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Filter</strong></td>
                        <td>: ' . ($filterInfo ?: 'Semua Kelas') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Cetak</strong></td>
                        <td>: ' . date('d/m/Y H:i:s') . '</td>
                    </tr>
                </table>
            </div>';
        
        // Summary table
        $html .= '
            <h3>Ringkasan Presensi</h3>
            <table class="summary-table">
                <tr>
                    <th>Tanggal</th>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpha</th>
                    <th>Total</th>
                </tr>';
        
        foreach ($summaryByDate as $date => $summary) {
            $html .= '
                <tr>
                    <td>' . date('d/m/Y', strtotime($date)) . '</td>
                    <td class="status-hadir">' . $summary['hadir'] . '</td>
                    <td class="status-izin">' . $summary['izin'] . '</td>
                    <td class="status-sakit">' . $summary['sakit'] . '</td>
                    <td class="status-alfa">' . $summary['alfa'] . '</td>
                    <td>' . $summary['total'] . '</td>
                </tr>';
        }
        
        $html .= '</table>';
        
        // Detail table
        $html .= '
            <h3>Detail Presensi</h3>
            <table class="data">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>';
        
        $no = 1;
        foreach ($absensiList as $item) {
            $statusClass = 'status-' . strtolower($item['status']);
            $html .= '
                <tr>
                    <td>' . $no++ . '</td>
                    <td>' . date('d/m/Y', strtotime($item['tanggal'])) . '</td>
                    <td>' . ($item['nis'] ?? '-') . '</td>
                    <td style="text-align: left;">' . ($item['nama_siswa'] ?? '-') . '</td>
                    <td>' . ($item['nama_kelas'] ?? '-') . '</td>
                    <td class="' . $statusClass . '">' . ucfirst($item['status']) . '</td>
                    <td style="text-align: left;">' . ($item['keterangan'] ?? '-') . '</td>
                </tr>';
        }
        
        $html .= '
            </table>
            
            <div class="footer">
                <div class="signature">
                    <p>Admin</p>
                    <br><br><br>
                    <p>_____________________</p>
                </div>
            </div>
        </body>
        </html>';
        
        // Generate PDF using DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Rekap_Presensi_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
        exit();
    }
}