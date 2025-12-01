<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JadwalModel;
use App\Models\JurusanModel;
use App\Models\GuruModel;
use App\Models\HariAbsensiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Absensi extends BaseController {
    protected $absensiModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $jadwalModel;
    protected $jurusanModel;
    protected $hariAbsensiModel;
    protected $guruId;

    public function __construct() {
        $this->absensiModel = new AbsensiModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jadwalModel = new JadwalModel();
        $this->jurusanModel = new JurusanModel();
        $this->hariAbsensiModel = new HariAbsensiModel();
        
        // Get guru ID from session
        $guruModel = new GuruModel();
        $this->guruId = $guruModel->getGuruByUserId(session('user_id'));
    }

    // Step 1: Tampilkan daftar kelas yang diajar guru langsung
    public function index() {
        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            $data = [
                'title' => 'Data Presensi',
                'kelasData' => [],
                'error' => 'Data guru Anda belum lengkap. Silakan hubungi admin untuk melengkapi data guru.'
            ];
            return view('guru/absensi/index', $data);
        }
        
        // Get kelas yang diajar guru beserta mata pelajaran
        $db = \Config\Database::connect();
        $jadwalGuru = $db->table('jadwal j')
            ->select('j.id as jadwal_id, j.hari, j.jam_mulai, j.jam_selesai, 
                      CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas,
                      k.id as kelas_id, mp.nama as mata_pelajaran, mp.id as mapel_id,
                      jur.nama_jurusan')
            ->join('kelas k', 'k.id = j.kelas_id')
            ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
            ->join('jurusan jur', 'jur.id = k.jurusan_id')
            ->where('j.guru_id', $guruId)
            ->orderBy('k.tingkat, k.kode_jurusan, k.paralel')
            ->get()
            ->getResultArray();
        
        // Group by kelas
        $kelasData = [];
        foreach ($jadwalGuru as $jadwal) {
            $kelasId = $jadwal['kelas_id'];
            if (!isset($kelasData[$kelasId])) {
                $kelasData[$kelasId] = [
                    'kelas_id' => $kelasId,
                    'nama_kelas' => $jadwal['nama_kelas'],
                    'nama_jurusan' => $jadwal['nama_jurusan'],
                    'mata_pelajaran' => []
                ];
            }
            
            $kelasData[$kelasId]['mata_pelajaran'][] = [
                'jadwal_id' => $jadwal['jadwal_id'],
                'nama' => $jadwal['mata_pelajaran'],
                'hari' => $jadwal['hari'],
                'jam' => $jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai']
            ];
        }
        
        $data = [
            'title' => 'Data Presensi',
            'kelasData' => array_values($kelasData)
        ];

        return view('guru/absensi/index', $data);
    }

    // Step 2: Tampilkan kelas berdasarkan jurusan yang diajar guru
    public function kelas($jurusanId = null) {
        if (!$jurusanId) {
            return redirect()->to('guru/absensi')->with('error', 'Jurusan tidak ditemukan');
        }

        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $jurusan = $this->jurusanModel->find($jurusanId);
        
        if (!$jurusan) {
            return redirect()->to('guru/absensi')->with('error', 'Jurusan tidak ditemukan');
        }

        // Validate guru has access to this jurusan
        $guruJurusan = $this->getJurusanByGuru($guruId);
        $hasAccess = false;
        foreach ($guruJurusan as $gj) {
            if ($gj['id'] == $jurusanId) {
                $hasAccess = true;
                break;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->to('guru/absensi')->with('error', 'Anda tidak memiliki akses ke jurusan ini');
        }

        // Get kelas yang memiliki jadwal yang diajar guru di jurusan tertentu
        $kelas = $this->getKelasByGuruAndJurusan($guruId, $jurusanId);

        $data = [
            'title' => 'Kelas - ' . $jurusan['nama_jurusan'],
            'jurusan' => $jurusan,
            'kelas' => $kelas
        ];

        return view('guru/absensi/kelas', $data);
    }

    // Step 3: Tampilkan jadwal berdasarkan kelas yang diajar guru
    public function jadwal($kelasId = null) {
        if (!$kelasId) {
            return redirect()->to('guru/absensi')->with('error', 'Kelas tidak ditemukan');
        }

        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get kelas info dengan jurusan_id dan verifikasi guru
        $db = \Config\Database::connect();
        $kelas = $db->table('kelas k')
                    ->select('k.*, jur.nama_jurusan')
                    ->join('jurusan jur', 'jur.id = k.jurusan_id')
                    ->where('k.id', $kelasId)
                    ->get()
                    ->getRowArray();

        if (!$kelas) {
            return redirect()->to('guru/absensi')->with('error', 'Kelas tidak ditemukan');
        }

        // Validate guru has access to this kelas through their schedules
        $guruKelas = $this->getKelasByGuruAndJurusan($guruId, $kelas['jurusan_id']);
        $hasAccess = false;
        foreach ($guruKelas as $gk) {
            if ($gk['id'] == $kelasId) {
                $hasAccess = true;
                break;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->to('guru/absensi/kelas/' . $kelas['jurusan_id'])->with('error', 'Anda tidak memiliki akses ke kelas ini');
        }

        // Get jadwal yang diajar guru di kelas tertentu
        $jadwal = $this->getJadwalByGuruAndKelas($guruId, $kelasId);

        $data = [
            'title' => 'Jadwal - ' . $kelas['tingkat'] . ' ' . $kelas['kode_jurusan'] . ' ' . $kelas['paralel'],
            'kelas' => $kelas,
            'jadwal' => $jadwal
        ];

        return view('guru/absensi/jadwal', $data);
    }

    // Step 4: Tampilkan daftar hari absensi berdasarkan jadwal
    public function hari($jadwalId = null) {
        if (!$jadwalId) {
            return redirect()->to('guru/absensi')->with('error', 'Jadwal tidak ditemukan');
        }

        $guruId = $this->guruId;

        // Get jadwal info dan verifikasi guru
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
            return redirect()->to('guru/absensi')->with('error', 'Jadwal tidak ditemukan atau Anda tidak berhak mengakses');
        }

        // Get daftar hari absensi yang sudah dibuat untuk jadwal ini
        $hariAbsensi = $this->getHariAbsensiByJadwal($jadwalId);

        $data = [
            'title' => 'Daftar Hari Presensi - ' . $jadwal['nama_mata_pelajaran'],
            'jadwal' => $jadwal,
            'hari_absensi' => $hariAbsensi
        ];

        return view('guru/absensi/hari', $data);
    }

    // Step 5: Form input absensi untuk hari tertentu
    public function inputAbsensi($hariAbsensiId = null) {
        if (!$hariAbsensiId) {
            return redirect()->to('guru/absensi')->with('error', 'Hari absensi tidak ditemukan');
        }

        $guruId = $this->guruId;

        // Get hari absensi info dan verifikasi guru
        $db = \Config\Database::connect();
        $hariAbsensi = $db->table('hari_absensi ha')
                         ->select('ha.*, j.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, jur.nama_jurusan, mp.nama as nama_mata_pelajaran')
                         ->join('jadwal j', 'j.id = ha.jadwal_id')
                         ->join('kelas k', 'k.id = j.kelas_id')
                         ->join('jurusan jur', 'jur.id = k.jurusan_id')
                         ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                         ->where('ha.id', $hariAbsensiId)
                         ->where('j.guru_id', $guruId) // Pastikan guru yang mengajar
                         ->get()
                         ->getRowArray();

        if (!$hariAbsensi) {
            return redirect()->to('guru/absensi')->with('error', 'Hari absensi tidak ditemukan atau Anda tidak berhak mengakses');
        }

        // Get siswa berdasarkan kelas di jadwal
        $siswa = $this->getSiswaByJadwal($hariAbsensi['jadwal_id']);

        // Get absensi yang sudah ada untuk hari ini
        $absensiExisting = $this->getAbsensiByHariAbsensi($hariAbsensiId);
        $absensiFormatted = [];
        
        foreach ($absensiExisting as $absensi) {
            $absensiFormatted[$absensi['siswa_id']] = [
                'status' => $absensi['status'],
                'keterangan' => $absensi['keterangan']
            ];
        }

        $data = [
            'title' => 'Input Presensi - ' . $hariAbsensi['nama_mata_pelajaran'] . ' (' . date('d/m/Y', strtotime($hariAbsensi['tanggal'])) . ')',
            'hari_absensi' => $hariAbsensi,
            'jadwal' => $hariAbsensi,
            'siswa' => $siswa,
            'absensi' => $absensiFormatted
        ];

        return view('guru/absensi/input', $data);
    }

    // Form untuk membuat hari absensi baru
    public function createHari($jadwalId = null) {
        if (!$jadwalId) {
            return redirect()->to('guru/absensi')->with('error', 'Jadwal tidak ditemukan');
        }

        $guruId = $this->guruId;

        // Get jadwal info dan verifikasi guru
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
            return redirect()->to('guru/absensi')->with('error', 'Jadwal tidak ditemukan atau Anda tidak berhak mengakses');
        }

        $data = [
            'title' => 'Buat Hari Presensi Baru - ' . $jadwal['nama_mata_pelajaran'],
            'jadwal' => $jadwal
        ];

        return view('guru/absensi/create_hari', $data);
    }

    // Simpan hari absensi baru
    public function storeHari() {
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        $keterangan = $this->request->getPost('keterangan');

        $rules = [
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'keterangan' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Cek apakah hari absensi untuk jadwal dan tanggal ini sudah ada
        $db = \Config\Database::connect();
        $existing = $db->table('hari_absensi')
                      ->where('jadwal_id', $jadwalId)
                      ->where('tanggal', $tanggal)
                      ->get()
                      ->getRowArray();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Hari absensi untuk tanggal tersebut sudah ada');
        }

        $hariAbsensiData = [
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db->table('hari_absensi')->insert($hariAbsensiData);
        $hariAbsensiId = $db->insertID();

        return redirect()->to('guru/absensi/input/' . $hariAbsensiId)->with('success', 'Hari absensi berhasil dibuat');
    }

    // Simpan absensi untuk hari tertentu
    public function storeAbsensi() {
        $hariAbsensiId = $this->request->getPost('hari_absensi_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $siswaIds = $this->request->getPost('siswa_id');
        $statuses = $this->request->getPost('status');
        $keterangans = $this->request->getPost('keterangan');

        if (!$hariAbsensiId || !$siswaIds || !$statuses) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus absensi lama untuk hari ini
            $db->table('absensi')->where('hari_absensi_id', $hariAbsensiId)->delete();

            // Insert absensi baru
            for ($i = 0; $i < count($siswaIds); $i++) {
                if (!empty($statuses[$i])) {
                    $absensiRecord = [
                        'siswa_id' => $siswaIds[$i],
                        'hari_absensi_id' => $hariAbsensiId,
                        'status' => $statuses[$i],
                        'keterangan' => $keterangans[$i] ?? '',
                        'jadwal_id'  => $jadwalId,
                        'tanggal'    => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $db->table('absensi')->insert($absensiRecord);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return redirect()->back()->with('error', 'Gagal menyimpan absensi');
            }

            return redirect()->back()->with('success', 'Absensi berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Helper methods untuk mendapatkan data berdasarkan guru
    private function getJurusanByGuru($guruId) {
        $db = \Config\Database::connect();
        return $db->table('jurusan jur')
                  ->select('jur.*')
                  ->join('kelas k', 'k.jurusan_id = jur.id')
                  ->join('jadwal j', 'j.kelas_id = k.id')
                  ->where('j.guru_id', $guruId)
                  ->groupBy('jur.id')
                  ->get()
                  ->getResultArray();
    }

    private function getKelasByGuruAndJurusan($guruId, $jurusanId) {
        $db = \Config\Database::connect();
        return $db->table('kelas k')
                  ->select('k.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                  ->join('jadwal j', 'j.kelas_id = k.id')
                  ->where('j.guru_id', $guruId)
                  ->where('k.jurusan_id', $jurusanId)
                  ->groupBy('k.id')
                  ->get()
                  ->getResultArray();
    }

    private function getJadwalByGuruAndKelas($guruId, $kelasId) {
        $db = \Config\Database::connect();
        return $db->table('jadwal j')
                  ->select('j.*, mp.nama as nama_mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai')
                  ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                  ->where('j.guru_id', $guruId)
                  ->where('j.kelas_id', $kelasId)
                  ->get()
                  ->getResultArray();
    }

    private function getHariAbsensiByJadwal($jadwalId) {
        $db = \Config\Database::connect();
        return $db->table('hari_absensi ha')
                  ->select('ha.*, COUNT(a.id) as jumlah_absensi')
                  ->join('absensi a', 'a.hari_absensi_id = ha.id', 'left')
                  ->where('ha.jadwal_id', $jadwalId)
                  ->groupBy('ha.id')
                  ->orderBy('ha.tanggal', 'DESC')
                  ->get()
                  ->getResultArray();
    }

    private function getSiswaByJadwal($jadwalId) {
        $db = \Config\Database::connect();
        return $db->table('siswa s')
                  ->select('s.*, u.full_name')
                  ->join('jadwal j', 'j.kelas_id = s.kelas_id')
                  ->join('users u', 'u.id = s.user_id')
                  ->where('j.id', $jadwalId)
                  ->orderBy('u.full_name')
                  ->get()
                  ->getResultArray();
    }

    private function getAbsensiByHariAbsensi($hariAbsensiId) {
        $db = \Config\Database::connect();
        return $db->table('absensi a')
                  ->select('a.*')
                  ->where('a.hari_absensi_id', $hariAbsensiId)
                  ->get()
                  ->getResultArray();
    }

    // Export functions with enhanced scope
    public function exportHari($hariAbsensiId) {
        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get hari absensi info dan verifikasi guru
        $db = \Config\Database::connect();
        $hariAbsensi = $db->table('hari_absensi ha')
                         ->select('ha.*, j.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, mp.nama as nama_mata_pelajaran')
                         ->join('jadwal j', 'j.id = ha.jadwal_id')
                         ->join('kelas k', 'k.id = j.kelas_id')
                         ->join('jurusan jur', 'jur.id = k.jurusan_id')
                         ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                         ->where('ha.id', $hariAbsensiId)
                         ->where('j.guru_id', $guruId)
                         ->get()
                         ->getRowArray();

        if (!$hariAbsensi) {
            return redirect()->back()->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses');
        }

        // Get data absensi
        $absensi = $db->table('absensi a')
                     ->select('a.*, u.full_name as nama_siswa, s.nis')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('users u', 'u.id = s.user_id')
                     ->where('a.hari_absensi_id', $hariAbsensiId)
                     ->orderBy('u.full_name')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Hari ' . date('d-m-Y', strtotime($hariAbsensi['tanggal'])) . ' - ' . $hariAbsensi['nama_mata_pelajaran']);
    }

    public function exportJadwal($jadwalId) {
        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get jadwal info dan verifikasi guru
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal j')
                    ->select('j.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, mp.nama as nama_mata_pelajaran')
                    ->join('kelas k', 'k.id = j.kelas_id')
                    ->join('jurusan jur', 'jur.id = k.jurusan_id')
                    ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                    ->where('j.id', $jadwalId)
                    ->where('j.guru_id', $guruId)
                    ->get()
                    ->getRowArray();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Data tidak ditemukan atau Anda tidak memiliki akses');
        }

        // Get data absensi untuk jadwal ini
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

    public function exportKelas($kelasId) {
        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get kelas info dan verifikasi guru memiliki akses
        $db = \Config\Database::connect();
        $kelas = $db->table('kelas k')
                    ->select('k.*, jur.nama_jurusan')
                    ->join('jurusan jur', 'jur.id = k.jurusan_id')
                    ->where('k.id', $kelasId)
                    ->get()
                    ->getRowArray();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        // Validate guru has access to this kelas
        $guruKelas = $this->getKelasByGuruAndJurusan($guruId, $kelas['jurusan_id']);
        $hasAccess = false;
        foreach ($guruKelas as $gk) {
            if ($gk['id'] == $kelasId) {
                $hasAccess = true;
                break;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini');
        }

        // Get data absensi untuk semua jadwal guru di kelas ini
        $absensi = $db->table('absensi a')
                     ->select('a.*, u.full_name as nama_siswa, s.nis, ha.tanggal, mp.nama as nama_mata_pelajaran')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->join('users u', 'u.id = s.user_id')
                     ->where('j.kelas_id', $kelasId)
                     ->where('j.guru_id', $guruId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('mp.nama')
                     ->orderBy('u.full_name')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Kelas - ' . $kelas['tingkat'] . ' ' . $kelas['kode_jurusan'] . ' ' . $kelas['paralel']);
    }

    public function exportJurusan($jurusanId) {
        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get jurusan info dan verifikasi guru memiliki akses
        $jurusan = $this->jurusanModel->find($jurusanId);
        
        if (!$jurusan) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        // Validate guru has access to this jurusan
        $guruJurusan = $this->getJurusanByGuru($guruId);
        $hasAccess = false;
        foreach ($guruJurusan as $gj) {
            if ($gj['id'] == $jurusanId) {
                $hasAccess = true;
                break;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke jurusan ini');
        }

        // Get data absensi untuk semua jadwal guru di jurusan ini
        $db = \Config\Database::connect();
        $absensi = $db->table('absensi a')
                     ->select('a.*, u.full_name as nama_siswa, s.nis, ha.tanggal, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                     ->join('siswa s', 's.id = a.siswa_id')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('kelas k', 'k.id = j.kelas_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->join('users u', 'u.id = s.user_id')
                     ->where('k.jurusan_id', $jurusanId)
                     ->where('j.guru_id', $guruId)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->orderBy('k.tingkat')
                     ->orderBy('k.kode_jurusan')
                     ->orderBy('k.paralel')
                     ->orderBy('mp.nama')
                     ->orderBy('u.full_name')
                     ->get()
                     ->getResultArray();

        return $this->generateExcel($absensi, 'Absensi Jurusan - ' . $jurusan['nama_jurusan']);
    }

    private function generateExcel($data, $title) {
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
    public function create() {
        $data = [
            'title' => 'Tambah Presensi',
            'siswa' => $this->siswaModel->getSiswaWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];
        return view('guru/absensi/create', $data);
    }
    public function store() {
        $siswaId = $this->request->getPost('siswa_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        $existingAbsensi = $this->absensiModel->checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal);
        if ($existingAbsensi) {
            return redirect()->back()->withInput()->with('error', 'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada');
        }
        $rules = [
            'siswa_id' => 'required|numeric',
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
            'keterangan' => 'permit_empty|max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $absensiData = [
            'siswa_id' => $siswaId,
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->absensiModel->insert($absensiData);
        return redirect()->to('guru/absensi')->with('success', 'Absensi berhasil ditambahkan');
    }
    public function edit($id = null) {
        $absensi = $this->absensiModel->getAbsensiWithRelations($id);
        if (!$absensi) {
            return redirect()->to('guru/absensi')->with('error', 'Absensi tidak ditemukan');
        }
        $data = [
            'title' => 'Edit Presensi',
            'absensi' => $absensi,
            'siswa' => $this->siswaModel->getSiswaWithRelations(),
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'jadwal' => $this->jadwalModel->getJadwalWithRelations()
        ];
        return view('guru/absensi/edit', $data);
    }
    public function update($id = null) {
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to('guru/absensi')->with('error', 'Absensi tidak ditemukan');
        }
        $siswaId = $this->request->getPost('siswa_id');
        $jadwalId = $this->request->getPost('jadwal_id');
        $tanggal = $this->request->getPost('tanggal');
        $existingAbsensi = $this->absensiModel->checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal, $id);
        if ($existingAbsensi) {
            return redirect()->back()->withInput()->with('error', 'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada');
        }
        $rules = [
            'siswa_id' => 'required|numeric',
            'jadwal_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
            'keterangan' => 'permit_empty|max_length[255]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $absensiData = [
            'siswa_id' => $siswaId,
            'jadwal_id' => $jadwalId,
            'tanggal' => $tanggal,
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->absensiModel->update($id, $absensiData);
        return redirect()->to('guru/absensi')->with('success', 'Absensi berhasil diperbarui');
    }
    public function delete($id = null) {
        $absensi = $this->absensiModel->find($id);
        if (!$absensi) {
            return redirect()->to('guru/absensi')->with('error', 'Absensi tidak ditemukan');
        }
        $this->absensiModel->delete($id);
        return redirect()->to('guru/absensi')->with('success', 'Absensi berhasil dihapus');
    }
    public function export()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kelasId = $this->request->getGet('kelas_id');
        $absensiList = $this->absensiModel->getAbsensiWithRelationsFiltered($startDate, $endDate, $kelasId);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Nama Siswa');
        $sheet->setCellValue('D1', 'NIS');
        $sheet->setCellValue('E1', 'Kelas');
        $sheet->setCellValue('F1', 'Jurusan');
        $sheet->setCellValue('G1', 'Mata Pelajaran');
        $sheet->setCellValue('H1', 'Guru');
        $sheet->setCellValue('I1', 'Hari');
        $sheet->setCellValue('J1', 'Jam');
        $sheet->setCellValue('K1', 'Status');
        $sheet->setCellValue('L1', 'Keterangan');
        $row = 2;
        $no = 1;
        foreach ($absensiList as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['tanggal']);
            $sheet->setCellValue('C' . $row, $item['nama_siswa']);
            $sheet->setCellValue('D' . $row, $item['nis']);
            $sheet->setCellValue('E' . $row, $item['nama_kelas']);
            $sheet->setCellValue('F' . $row, $item['nama_jurusan']);
            $sheet->setCellValue('G' . $row, $item['mata_pelajaran']);
            $sheet->setCellValue('H' . $row, $item['nama_guru']);
            $sheet->setCellValue('I' . $row, $item['hari']);
            $sheet->setCellValue('J' . $row, $item['jam_mulai'] . ' - ' . $item['jam_selesai']);
            $sheet->setCellValue('K' . $row, $item['status']);
            $sheet->setCellValue('L' . $row, $item['keterangan']);
            $row++;
        }
        $filename = 'absensi_guru_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    
    // Export rekap presensi ke PDF
    public function exportPdf()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kelasId = $this->request->getGet('kelas_id');
        
        $absensiList = $this->absensiModel->getAbsensiWithRelationsFiltered($startDate, $endDate, $kelasId);
        
        // Get guru info
        $db = \Config\Database::connect();
        $guru = $db->table('guru g')
                   ->select('g.*, u.full_name as nama_guru')
                   ->join('users u', 'u.id = g.user_id')
                   ->where('g.id', $this->guruId)
                   ->get()
                   ->getRowArray();
        
        // Get kelas info if filtered
        $kelasInfo = '';
        if ($kelasId) {
            $kelas = $this->kelasModel->find($kelasId);
            if ($kelas) {
                $kelasInfo = $kelas['tingkat'] . ' ' . $kelas['kode_jurusan'] . ' ' . $kelas['paralel'];
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
            $summaryByDate[$date][$item['status']]++;
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
                table.data tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                .summary {
                    margin-top: 20px;
                }
                .summary h3 {
                    font-size: 13px;
                    margin-bottom: 10px;
                }
                .status-hadir { color: green; font-weight: bold; }
                .status-izin { color: blue; font-weight: bold; }
                .status-sakit { color: orange; font-weight: bold; }
                .status-alfa { color: red; font-weight: bold; }
                .footer {
                    margin-top: 30px;
                    text-align: right;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>REKAP PRESENSI SISWA</h1>
                <h2>Sistem Pembelajaran E-Learning</h2>
            </div>
            
            <div class="info">
                <table>
                    <tr>
                        <td><strong>Nama Guru</strong></td>
                        <td>: ' . esc($guru['nama_guru'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Bidang Studi</strong></td>
                        <td>: ' . esc($guru['bidang_studi'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Kelas</strong></td>
                        <td>: ' . ($kelasInfo ?: 'Semua Kelas') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Periode</strong></td>
                        <td>: ' . ($startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua') . ' s/d ' . ($endDate ? date('d/m/Y', strtotime($endDate)) : 'Semua') . '</td>
                    </tr>
                </table>
            </div>
            
            <table class="data">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>';
        
        $no = 1;
        foreach ($absensiList as $item) {
            $statusClass = 'status-' . strtolower($item['status']);
            $html .= '<tr>
                        <td>' . $no++ . '</td>
                        <td>' . date('d/m/Y', strtotime($item['tanggal'])) . '</td>
                        <td style="text-align: left;">' . esc($item['nama_siswa']) . '</td>
                        <td>' . esc($item['nis']) . '</td>
                        <td>' . esc($item['nama_kelas']) . '</td>
                        <td style="text-align: left;">' . esc($item['mata_pelajaran']) . '</td>
                        <td>' . esc($item['hari']) . '</td>
                        <td>' . esc($item['jam_mulai'] . '-' . $item['jam_selesai']) . '</td>
                        <td class="' . $statusClass . '">' . esc(ucfirst($item['status'])) . '</td>
                        <td style="text-align: left;">' . esc($item['keterangan'] ?? '-') . '</td>
                    </tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="summary">
                <h3>Ringkasan Presensi per Tanggal</h3>
                <table class="data">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alfa</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlfa = 0;
        $totalAll = 0;
        
        foreach ($summaryByDate as $date => $summary) {
            $html .= '<tr>
                        <td>' . date('d/m/Y', strtotime($date)) . '</td>
                        <td class="status-hadir">' . $summary['hadir'] . '</td>
                        <td class="status-izin">' . $summary['izin'] . '</td>
                        <td class="status-sakit">' . $summary['sakit'] . '</td>
                        <td class="status-alfa">' . $summary['alfa'] . '</td>
                        <td>' . $summary['total'] . '</td>
                    </tr>';
            $totalHadir += $summary['hadir'];
            $totalIzin += $summary['izin'];
            $totalSakit += $summary['sakit'];
            $totalAlfa += $summary['alfa'];
            $totalAll += $summary['total'];
        }
        
        $html .= '<tr style="font-weight: bold; background-color: #e9ecef;">
                    <td>TOTAL</td>
                    <td class="status-hadir">' . $totalHadir . '</td>
                    <td class="status-izin">' . $totalIzin . '</td>
                    <td class="status-sakit">' . $totalSakit . '</td>
                    <td class="status-alfa">' . $totalAlfa . '</td>
                    <td>' . $totalAll . '</td>
                </tr>
                </tbody>
            </table>
            </div>
            
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

        $filename = 'Rekap_Presensi_' . date('Y-m-d') . '.pdf';
        
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
    
    public function getJadwalByKelas($kelasId) {
        $jadwal = $this->jadwalModel->getJadwalByKelas($kelasId);
        return $this->response->setJSON($jadwal);
    }
    public function getSiswaByKelas($kelasId) {
        $siswa = $this->siswaModel->getSiswaByKelas($kelasId);
        return $this->response->setJSON($siswa);
    }
}