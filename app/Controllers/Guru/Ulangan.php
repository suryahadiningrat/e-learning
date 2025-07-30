<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\UlanganModel;
use App\Models\JadwalModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class Ulangan extends BaseController
{
    protected $ulanganModel;
    protected $jadwalModel;
    protected $kelasModel;
    protected $mataPelajaranModel;
    protected $db;

    public function __construct()
    {
        $this->ulanganModel = new UlanganModel();
        $this->jadwalModel = new JadwalModel();
        $this->kelasModel = new KelasModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        // Get ulangan yang dibuat oleh guru
        $ulangan = $this->ulanganModel->getUlanganByGuru($guruId);

        $data = [
            'title' => 'Data Ulangan',
            'ulangan' => $ulangan
        ];

        return view('guru/ulangan/index', $data);
    }

    public function create()
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        // Get mata pelajaran yang diajar oleh guru
        $mataPelajaran = $this->db->table('jadwal j')
                                 ->select('j.mata_pelajaran_id as id, mp.nama')
                                 ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                                 ->where('j.guru_id', $guruId)
                                 ->groupBy('j.mata_pelajaran_id, mp.nama')
                                 ->get()
                                 ->getResultArray();
        
        // Get kelas yang diajar oleh guru
        $kelas = $this->db->table('jadwal j')
                         ->select('j.kelas_id as id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, COALESCE(jur.nama_jurusan, "Umum") as nama_jurusan')
                         ->join('kelas k', 'k.id = j.kelas_id')
                         ->join('jurusan jur', 'jur.id = k.jurusan_id', 'left')
                         ->where('j.guru_id', $guruId)
                         ->groupBy('j.kelas_id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
                         ->get()
                         ->getResultArray();
        
        $data = [
            'title' => 'Tambah Ulangan',
            'mata_pelajaran' => $mataPelajaran,
            'kelas' => $kelas
        ];

        return view('guru/ulangan/create', $data);
    }

    public function store()
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        // Debug: Log input data
        log_message('debug', 'Guru Ulangan Store - Input Data: ' . json_encode($this->request->getPost()));
        
        // Debug: Log soal_json specifically
        $soalJson = $this->request->getPost('soal_json');
        log_message('debug', 'Guru Ulangan Store - Soal JSON Raw: ' . $soalJson);
        
        // Validasi input
        $rules = [
            'judul_ulangan' => 'required|min_length[3]|max_length[255]',
            'mata_pelajaran_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'waktu_mulai' => 'required|valid_date',
            'waktu_selesai' => 'required|valid_date',
            'durasi_menit' => 'required|numeric|greater_than[0]',
            'soal_json' => 'required'
        ];

        $messages = [
            'judul_ulangan' => [
                'required' => 'Judul ulangan harus diisi',
                'min_length' => 'Judul minimal 3 karakter',
                'max_length' => 'Judul maksimal 255 karakter'
            ],
            'mata_pelajaran_id' => [
                'required' => 'Mata pelajaran harus dipilih',
                'numeric' => 'Mata pelajaran tidak valid'
            ],
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas tidak valid'
            ],
            'waktu_mulai' => [
                'required' => 'Waktu mulai harus diisi',
                'valid_date' => 'Format waktu mulai tidak valid'
            ],
            'waktu_selesai' => [
                'required' => 'Waktu selesai harus diisi',
                'valid_date' => 'Format waktu selesai tidak valid'
            ],
            'durasi_menit' => [
                'required' => 'Durasi harus diisi',
                'numeric' => 'Durasi harus berupa angka',
                'greater_than' => 'Durasi harus lebih dari 0'
            ],
            'soal_json' => [
                'required' => 'Soal harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            log_message('error', 'Guru Ulangan Store - Validation Errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi mata pelajaran (guru hanya bisa buat ulangan untuk mata pelajaran yang diajar)
        $mataPelajaranGuru = $this->db->table('jadwal j')
                                     ->select('j.mata_pelajaran_id')
                                     ->where('j.guru_id', $guruId)
                                     ->groupBy('j.mata_pelajaran_id')
                                     ->get()
                                     ->getResultArray();
        
        $mataPelajaranIds = array_column($mataPelajaranGuru, 'mata_pelajaran_id');
        
        if (!in_array($this->request->getPost('mata_pelajaran_id'), $mataPelajaranIds)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak berhak membuat ulangan untuk mata pelajaran ini');
        }

        // Validasi waktu selesai harus lebih besar dari waktu mulai
        $waktuMulai = strtotime($this->request->getPost('waktu_mulai'));
        $waktuSelesai = strtotime($this->request->getPost('waktu_selesai'));
        
        if ($waktuSelesai <= $waktuMulai) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih besar dari waktu mulai');
        }

        // Validasi soal_json
        if (empty($soalJson) || $soalJson === '{"soal":[]}' || $soalJson === '[]') {
            log_message('error', 'Guru Ulangan Store - Soal JSON is empty or invalid');
            return redirect()->back()->withInput()->with('error', 'Soal harus diisi minimal 1 soal');
        }

        // Validasi format JSON
        $soalData = json_decode($soalJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'Guru Ulangan Store - Invalid JSON format: ' . json_last_error_msg());
            return redirect()->back()->withInput()->with('error', 'Format soal tidak valid');
        }

        if (!isset($soalData['soal']) || !is_array($soalData['soal']) || empty($soalData['soal'])) {
            log_message('error', 'Guru Ulangan Store - Soal array is empty or invalid');
            return redirect()->back()->withInput()->with('error', 'Soal harus diisi minimal 1 soal');
        }

        // Simpan data ulangan
        $data = [
            'judul_ulangan' => $this->request->getPost('judul_ulangan'),
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'waktu_mulai' => $this->request->getPost('waktu_mulai'),
            'waktu_selesai' => $this->request->getPost('waktu_selesai'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'soal_json' => $soalJson,
            'status' => 'draft',
            'created_by' => $userId, // Gunakan user_id untuk created_by
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Debug: Log data yang akan disimpan
        log_message('debug', 'Guru Ulangan Store - Data to Insert: ' . json_encode($data));

        if ($this->ulanganModel->insert($data)) {
            $insertId = $this->ulanganModel->insertID();
            log_message('debug', 'Guru Ulangan Store - Success, Insert ID: ' . $insertId);
            return redirect()->to('guru/ulangan')->with('success', 'Ulangan berhasil ditambahkan');
        } else {
            // Get validation errors
            $errors = $this->ulanganModel->errors();
            $errorMessage = 'Gagal menambahkan ulangan';
            if (!empty($errors)) {
                $errorMessage .= ': ' . implode(', ', $errors);
            }
            log_message('error', 'Guru Ulangan Store - Insert Failed: ' . $errorMessage);
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    public function edit($id = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $id);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Debug: Log ulangan data
        log_message('debug', 'Guru Ulangan Edit - Ulangan Data: ' . json_encode($ulangan));

        // Get mata pelajaran yang diajar oleh guru
        $mataPelajaran = $this->db->table('jadwal j')
                                 ->select('j.mata_pelajaran_id as id, mp.nama')
                                 ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                                 ->where('j.guru_id', $guruId)
                                 ->groupBy('j.mata_pelajaran_id, mp.nama')
                                 ->get()
                                 ->getResultArray();
        
        // Get kelas yang diajar oleh guru
        $kelas = $this->db->table('jadwal j')
                         ->select('j.kelas_id as id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, COALESCE(jur.nama_jurusan, "Umum") as nama_jurusan')
                         ->join('kelas k', 'k.id = j.kelas_id')
                         ->join('jurusan jur', 'jur.id = k.jurusan_id', 'left')
                         ->where('j.guru_id', $guruId)
                         ->groupBy('j.kelas_id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
                         ->get()
                         ->getResultArray();
        
        // Debug: Log mata pelajaran and kelas data
        log_message('debug', 'Guru Ulangan Edit - Mata Pelajaran: ' . json_encode($mataPelajaran));
        log_message('debug', 'Guru Ulangan Edit - Kelas: ' . json_encode($kelas));
        
        $data = [
            'title' => 'Edit Ulangan',
            'ulangan' => $ulangan,
            'mata_pelajaran' => $mataPelajaran,
            'kelas' => $kelas
        ];

        return view('guru/ulangan/edit', $data);
    }

    public function update($id = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $id);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Validasi input
        $rules = [
            'judul_ulangan' => 'required|min_length[3]|max_length[255]',
            'mata_pelajaran_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'waktu_mulai' => 'required|valid_date',
            'waktu_selesai' => 'required|valid_date',
            'durasi_menit' => 'required|numeric|greater_than[0]',
            'soal_json' => 'required'
        ];

        $messages = [
            'judul_ulangan' => [
                'required' => 'Judul ulangan harus diisi',
                'min_length' => 'Judul minimal 3 karakter',
                'max_length' => 'Judul maksimal 255 karakter'
            ],
            'mata_pelajaran_id' => [
                'required' => 'Mata pelajaran harus dipilih',
                'numeric' => 'Mata pelajaran tidak valid'
            ],
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas tidak valid'
            ],
            'waktu_mulai' => [
                'required' => 'Waktu mulai harus diisi',
                'valid_date' => 'Format waktu mulai tidak valid'
            ],
            'waktu_selesai' => [
                'required' => 'Waktu selesai harus diisi',
                'valid_date' => 'Format waktu selesai tidak valid'
            ],
            'durasi_menit' => [
                'required' => 'Durasi harus diisi',
                'numeric' => 'Durasi harus berupa angka',
                'greater_than' => 'Durasi harus lebih dari 0'
            ],
            'soal_json' => [
                'required' => 'Soal harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi mata pelajaran (guru hanya bisa update ulangan untuk mata pelajaran yang diajar)
        $mataPelajaranGuru = $this->db->table('jadwal j')
                                     ->select('j.mata_pelajaran_id')
                                     ->where('j.guru_id', $guruId)
                                     ->groupBy('j.mata_pelajaran_id')
                                     ->get()
                                     ->getResultArray();
        
        $mataPelajaranIds = array_column($mataPelajaranGuru, 'mata_pelajaran_id');
        
        if (!in_array($this->request->getPost('mata_pelajaran_id'), $mataPelajaranIds)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak berhak mengupdate ulangan untuk mata pelajaran ini');
        }

        // Validasi waktu selesai harus lebih besar dari waktu mulai
        $waktuMulai = strtotime($this->request->getPost('waktu_mulai'));
        $waktuSelesai = strtotime($this->request->getPost('waktu_selesai'));
        
        if ($waktuSelesai <= $waktuMulai) {
            return redirect()->back()->withInput()->with('error', 'Waktu selesai harus lebih besar dari waktu mulai');
        }

        // Update data ulangan
        $data = [
            'judul_ulangan' => $this->request->getPost('judul_ulangan'),
            'mata_pelajaran_id' => $this->request->getPost('mata_pelajaran_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'waktu_mulai' => $this->request->getPost('waktu_mulai'),
            'waktu_selesai' => $this->request->getPost('waktu_selesai'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'soal_json' => $this->request->getPost('soal_json'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->ulanganModel->update($id, $data)) {
            return redirect()->to('guru/ulangan')->with('success', 'Ulangan berhasil diupdate');
        } else {
            // Get validation errors
            $errors = $this->ulanganModel->errors();
            $errorMessage = 'Gagal mengupdate ulangan';
            if (!empty($errors)) {
                $errorMessage .= ': ' . implode(', ', $errors);
            }
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    public function delete($id = null)
    {
        $guruId = session('user_id');
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $id);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        if ($this->ulanganModel->delete($id)) {
            return redirect()->to('guru/ulangan')->with('success', 'Ulangan berhasil dihapus');
        } else {
            return redirect()->to('guru/ulangan')->with('error', 'Gagal menghapus ulangan');
        }
    }

    public function preview($id = null)
    {
        $guruId = session('user_id');
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $id);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        $data = [
            'title' => 'Preview Ulangan',
            'ulangan' => $ulangan
        ];

        return view('guru/ulangan/preview', $data);
    }

    public function hasil($id = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $id);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        // Get hasil ulangan
        $hasil = $this->ulanganModel->getHasilUlangan($id);
        
        // Calculate statistics
        $totalSoal = 0;
        $totalBobot = 0;
        $nilaiArray = [];
        
        if (!empty($hasil)) {
            $soalJson = json_decode($ulangan['soal_json'], true);
            if (isset($soalJson['soal'])) {
                $totalSoal = count($soalJson['soal']);
                foreach ($soalJson['soal'] as $soal) {
                    $totalBobot += $soal['bobot'];
                }
            }
            
            foreach ($hasil as $item) {
                $nilaiArray[] = $item['nilai'];
            }
        }
        
        $rataRata = !empty($nilaiArray) ? array_sum($nilaiArray) / count($nilaiArray) : 0;
        $nilaiTertinggi = !empty($nilaiArray) ? max($nilaiArray) : 0;
        $nilaiTerendah = !empty($nilaiArray) ? min($nilaiArray) : 0;

        $data = [
            'title' => 'Hasil Ulangan',
            'ulangan' => $ulangan,
            'hasil' => $hasil,
            'totalSoal' => $totalSoal,
            'totalBobot' => $totalBobot,
            'rataRata' => $rataRata,
            'nilaiTertinggi' => $nilaiTertinggi,
            'nilaiTerendah' => $nilaiTerendah
        ];

        return view('guru/ulangan/hasil', $data);
    }

    public function detailHasil($ulanganId = null, $siswaId = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        // Get ulangan dan validasi kepemilikan
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $ulanganId);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }
        
        // Get detail hasil ulangan dengan data siswa
        $hasil = $this->db->table('hasil_ulangan hu')
                         ->select('hu.*, s.nis, u.full_name as nama_siswa, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                         ->join('siswa s', 's.id = hu.siswa_id')
                         ->join('users u', 'u.id = s.user_id')
                         ->join('kelas k', 'k.id = s.kelas_id')
                         ->where('hu.ulangan_id', $ulanganId)
                         ->where('hu.siswa_id', $siswaId)
                         ->get()
                         ->getRowArray();
        
        if (!$hasil) {
            return redirect()->to('guru/ulangan/hasil/' . $ulanganId)->with('error', 'Hasil ulangan tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Hasil Ulangan',
            'ulangan' => $ulangan,
            'hasil' => $hasil,
            'siswa' => [
                'nama' => $hasil['nama_siswa'],
                'nis' => $hasil['nis'],
                'nama_kelas' => $hasil['nama_kelas']
            ]
        ];

        return view('guru/ulangan/detail_hasil', $data);
    }

    public function publish($id = null)
    {
        $guruId = session('user_id');
        $ulangan = $this->ulanganModel->getUlanganByGuruAndId($guruId, $id);
        
        if (!$ulangan) {
            return redirect()->to('guru/ulangan')->with('error', 'Ulangan tidak ditemukan');
        }

        if ($this->ulanganModel->update($id, ['status' => 'published'])) {
            return redirect()->to('guru/ulangan')->with('success', 'Ulangan berhasil dipublish');
        } else {
            return redirect()->to('guru/ulangan')->with('error', 'Gagal mempublish ulangan');
        }
    }
} 