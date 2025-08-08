<?php

namespace App\Controllers\Admin;

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
        $data = [
            'title' => 'Data Tugas',
            'tugas' => $this->tugasModel->getAllTugasForAdmin()
        ];

        return view('admin/tugas/index', $data);
    }

    public function create()
    {
        // Get all jadwal for dropdown
        $jadwal = $this->db->table('jadwal j')
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, 
                                   j.hari, j.jam_mulai, j.jam_selesai, u.full_name as nama_guru')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->join('guru g', 'g.id = j.guru_id')
                          ->join('users u', 'u.id = g.user_id')
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        $data = [
            'title' => 'Tambah Tugas',
            'jadwal' => $jadwal
        ];

        return view('admin/tugas/create', $data);
    }

    public function store()
    {
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
            return redirect()->to('admin/tugas')->with('success', 'Tugas berhasil ditambahkan');
        } else {
            $errors = $this->tugasModel->errors();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan tugas: ' . implode(', ', $errors));
        }
    }

    public function edit($id = null)
    {
        $tugas = $this->tugasModel->getTugasWithRelations($id);

        if (!$tugas) {
            return redirect()->to('admin/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Get all jadwal for dropdown
        $jadwal = $this->db->table('jadwal j')
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, 
                                   j.hari, j.jam_mulai, j.jam_selesai, u.full_name as nama_guru')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->join('guru g', 'g.id = j.guru_id')
                          ->join('users u', 'u.id = g.user_id')
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        // Get pengumpulan tugas
        $pengumpulan = $this->pengumpulanTugasModel->getPengumpulanByTugas($id);

        $data = [
            'title' => 'Edit Tugas',
            'tugas' => $tugas,
            'jadwal' => $jadwal,
            'pengumpulan' => $pengumpulan
        ];

        return view('admin/tugas/edit', $data);
    }

    public function update($id = null)
    {
        $tugas = $this->tugasModel->find($id);

        if (!$tugas) {
            return redirect()->to('admin/tugas')->with('error', 'Tugas tidak ditemukan');
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
            return redirect()->to('admin/tugas')->with('success', 'Tugas berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate tugas');
        }
    }

    public function delete($id = null)
    {
        $tugas = $this->tugasModel->find($id);

        if (!$tugas) {
            return redirect()->to('admin/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Check if there are submissions
        $submissions = $this->pengumpulanTugasModel->where('tugas_id', $id)->findAll();
        if (!empty($submissions)) {
            return redirect()->to('admin/tugas')->with('error', 'Tidak dapat menghapus tugas yang sudah ada pengumpulan');
        }

        if ($this->tugasModel->delete($id)) {
            return redirect()->to('admin/tugas')->with('success', 'Tugas berhasil dihapus');
        } else {
            return redirect()->to('admin/tugas')->with('error', 'Gagal menghapus tugas');
        }
    }

    public function detail($id = null)
    {
        $tugas = $this->tugasModel->getTugasWithRelations($id);

        if (!$tugas) {
            return redirect()->to('admin/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        $pengumpulan = $this->pengumpulanTugasModel->getPengumpulanByTugas($id);

        $data = [
            'title' => 'Detail Tugas',
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan
        ];

        return view('admin/tugas/detail', $data);
    }

    public function deletePengumpulan($id = null)
    {
        $pengumpulan = $this->pengumpulanTugasModel->find($id);

        if (!$pengumpulan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengumpulan tidak ditemukan'
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