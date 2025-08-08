<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\TugasModel;
use App\Models\PengumpulanTugasModel;

class Tugas extends BaseController
{
    protected $tugasModel;
    protected $pengumpulanTugasModel;
    protected $db;

    public function __construct()
    {
        $this->tugasModel = new TugasModel();
        $this->pengumpulanTugasModel = new PengumpulanTugasModel();
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

        // Get jadwal yang diajar oleh guru untuk modal add
        $jadwal = $this->db->table('jadwal j')
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, 
                                   j.hari, j.jam_mulai, j.jam_selesai')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->where('j.guru_id', $guruId)
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        $data = [
            'title' => 'Data Tugas',
            'tugas' => $this->tugasModel->getTugasByGuru($guruId),
            'jadwal' => $jadwal
        ];

        return view('guru/tugas/index', $data);
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

        // Get jadwal yang diajar oleh guru
        $jadwal = $this->db->table('jadwal j')
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, 
                                   j.hari, j.jam_mulai, j.jam_selesai')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->where('j.guru_id', $guruId)
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        $data = [
            'title' => 'Tambah Tugas',
            'jadwal' => $jadwal
        ];

        return view('guru/tugas/create', $data);
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

        $rules = [
            'nama_tugas' => 'required|min_length[3]|max_length[255]',
            'jadwal_id' => 'required|numeric',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'deadline' => 'permit_empty|valid_date'
        ];

        $messages = [
            'nama_tugas' => [
                'required' => 'Nama tugas harus diisi',
                'min_length' => 'Nama tugas minimal 3 karakter',
                'max_length' => 'Nama tugas maksimal 255 karakter'
            ],
            'jadwal_id' => [
                'required' => 'Jadwal harus dipilih',
                'numeric' => 'Jadwal tidak valid'
            ],
            'deskripsi' => [
                'max_length' => 'Deskripsi maksimal 1000 karakter'
            ],
            'deadline' => [
                'valid_date' => 'Format tanggal deadline tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi jadwal (guru hanya bisa membuat tugas untuk jadwal yang diajar)
        $jadwalGuru = $this->db->table('jadwal j')
                              ->select('j.id as jadwal_id')
                              ->where('j.guru_id', $guruId)
                              ->get()
                              ->getResultArray();
        
        $jadwalIds = array_column($jadwalGuru, 'jadwal_id');
        
        if (!in_array($this->request->getPost('jadwal_id'), $jadwalIds)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak berhak membuat tugas untuk jadwal ini');
        }

        $deadline = $this->request->getPost('deadline');
        if (!empty($deadline)) {
            $deadline = date('Y-m-d H:i:s', strtotime($deadline));
        } else {
            $deadline = null;
        }

        $data = [
            'nama_tugas' => $this->request->getPost('nama_tugas'),
            'jadwal_id' => $this->request->getPost('jadwal_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'deadline' => $deadline,
            'created_by' => session('user_id')
        ];

        if ($this->tugasModel->insert($data)) {
            return redirect()->to('guru/tugas')->with('success', 'Tugas berhasil ditambahkan');
        } else {
            $errors = $this->tugasModel->errors();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan tugas: ' . implode(', ', $errors));
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

        $tugas = $this->tugasModel->getTugasWithRelations($id);

        if (!$tugas) {
            return redirect()->to('guru/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Validasi kepemilikan tugas (guru hanya bisa edit tugas di jadwalnya)
        if (!$this->tugasModel->canGuruManageTugas($id, $guruId)) {
            return redirect()->to('guru/tugas')->with('error', 'Anda tidak berhak mengedit tugas ini');
        }

        // Get jadwal yang diajar oleh guru
        $jadwal = $this->db->table('jadwal j')
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, 
                                   j.hari, j.jam_mulai, j.jam_selesai')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->where('j.guru_id', $guruId)
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        $data = [
            'title' => 'Edit Tugas',
            'tugas' => $tugas,
            'jadwal' => $jadwal
        ];

        // If this is an AJAX request, return only the form content
        if ($this->request->isAJAX()) {
            return view('guru/tugas/edit_form', $data);
        }

        return view('guru/tugas/edit', $data);
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

        $tugas = $this->tugasModel->find($id);

        if (!$tugas) {
            return redirect()->to('guru/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Validasi kepemilikan tugas
        if (!$this->tugasModel->canGuruManageTugas($id, $guruId)) {
            return redirect()->to('guru/tugas')->with('error', 'Anda tidak berhak mengedit tugas ini');
        }

        $rules = [
            'nama_tugas' => 'required|min_length[3]|max_length[255]',
            'jadwal_id' => 'required|numeric',
            'deskripsi' => 'permit_empty|max_length[1000]',
            'deadline' => 'permit_empty|valid_date'
        ];

        $messages = [
            'nama_tugas' => [
                'required' => 'Nama tugas harus diisi',
                'min_length' => 'Nama tugas minimal 3 karakter',
                'max_length' => 'Nama tugas maksimal 255 karakter'
            ],
            'jadwal_id' => [
                'required' => 'Jadwal harus dipilih',
                'numeric' => 'Jadwal tidak valid'
            ],
            'deskripsi' => [
                'max_length' => 'Deskripsi maksimal 1000 karakter'
            ],
            'deadline' => [
                'valid_date' => 'Format tanggal deadline tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi jadwal
        $jadwalGuru = $this->db->table('jadwal j')
                              ->select('j.id as jadwal_id')
                              ->where('j.guru_id', $guruId)
                              ->get()
                              ->getResultArray();
        
        $jadwalIds = array_column($jadwalGuru, 'jadwal_id');
        
        if (!in_array($this->request->getPost('jadwal_id'), $jadwalIds)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak berhak mengubah tugas untuk jadwal ini');
        }

        $deadline = $this->request->getPost('deadline');
        if (!empty($deadline)) {
            $deadline = date('Y-m-d H:i:s', strtotime($deadline));
        } else {
            $deadline = null;
        }

        $data = [
            'nama_tugas' => $this->request->getPost('nama_tugas'),
            'jadwal_id' => $this->request->getPost('jadwal_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'deadline' => $deadline
        ];

        if ($this->tugasModel->update($id, $data)) {
            return redirect()->to('guru/tugas')->with('success', 'Tugas berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate tugas');
        }
    }

    public function delete($id = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];

        $tugas = $this->tugasModel->find($id);

        if (!$tugas) {
            return redirect()->to('guru/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Validasi kepemilikan tugas
        if (!$this->tugasModel->canGuruManageTugas($id, $guruId)) {
            return redirect()->to('guru/tugas')->with('error', 'Anda tidak berhak menghapus tugas ini');
        }

        // Check if there are submissions
        $submissions = $this->pengumpulanTugasModel->where('tugas_id', $id)->findAll();
        if (!empty($submissions)) {
            return redirect()->to('guru/tugas')->with('error', 'Tidak dapat menghapus tugas yang sudah ada pengumpulan');
        }

        if ($this->tugasModel->delete($id)) {
            return redirect()->to('guru/tugas')->with('success', 'Tugas berhasil dihapus');
        } else {
            return redirect()->to('guru/tugas')->with('error', 'Gagal menghapus tugas');
        }
    }

    public function detail($id = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];

        $tugas = $this->tugasModel->getTugasWithRelations($id);

        if (!$tugas) {
            return redirect()->to('guru/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Validasi kepemilikan tugas
        if (!$this->tugasModel->canGuruManageTugas($id, $guruId)) {
            return redirect()->to('guru/tugas')->with('error', 'Anda tidak berhak melihat detail tugas ini');
        }

        $pengumpulan = $this->pengumpulanTugasModel->getPengumpulanByTugas($id);

        $data = [
            'title' => 'Detail Tugas',
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan
        ];

        return view('guru/tugas/detail', $data);
    }

    public function deletePengumpulan($id = null)
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->db->table('guru')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$guru) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data guru tidak ditemukan'
            ]);
        }
        
        $guruId = $guru['id'];

        $pengumpulan = $this->pengumpulanTugasModel->find($id);

        if (!$pengumpulan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengumpulan tidak ditemukan'
            ]);
        }

        // Validasi bahwa guru berhak menghapus pengumpulan ini
        $tugasId = $pengumpulan['tugas_id'];
        if (!$this->tugasModel->canGuruManageTugas($tugasId, $guruId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak berhak menghapus pengumpulan ini'
            ]);
        }

        if ($this->pengumpulanTugasModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pengumpulan berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus pengumpulan'
            ]);
        }
    }
}
