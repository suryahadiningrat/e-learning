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
        
        // Get guru ID and validate session
        $guruModel = new GuruModel();
        $this->guruId = $guruModel->getGuruByUserId(session('user_id'));
        
        if (!$this->guruId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses tidak diizinkan');
        }
    }

    // Step 1: Tampilkan daftar jurusan yang memiliki jadwal yang diajar guru
    public function index() {
        $guruId = $this->guruId;
        
        // Validate guru access
        if (!$guruId) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get jurusan yang memiliki jadwal yang diajar guru
        $jurusan = $this->getJurusanByGuru($guruId);
        
        if (empty($jurusan)) {
            $data = [
                'title' => 'Data Absensi',
                'jurusan' => [],
                'message' => 'Anda belum memiliki jadwal mengajar'
            ];
        } else {
            $data = [
                'title' => 'Data Absensi',
                'jurusan' => $jurusan
            ];
        }

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
            'title' => 'Daftar Hari Absensi - ' . $jadwal['nama_mata_pelajaran'],
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
            'title' => 'Input Absensi - ' . $hariAbsensi['nama_mata_pelajaran'] . ' (' . date('d/m/Y', strtotime($hariAbsensi['tanggal'])) . ')',
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
            'title' => 'Buat Hari Absensi Baru - ' . $jadwal['nama_mata_pelajaran'],
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
            'title' => 'Tambah Absensi',
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
            'title' => 'Edit Absensi',
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
    public function getJadwalByKelas($kelasId) {
        $jadwal = $this->jadwalModel->getJadwalByKelas($kelasId);
        return $this->response->setJSON($jadwal);
    }
    public function getSiswaByKelas($kelasId) {
        $siswa = $this->siswaModel->getSiswaByKelas($kelasId);
        return $this->response->setJSON($siswa);
    }
}