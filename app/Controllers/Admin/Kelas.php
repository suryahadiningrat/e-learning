<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\JurusanModel;

class Kelas extends BaseController
{
    protected $kelasModel;
    protected $jurusanModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Kelas',
            'kelas' => $this->kelasModel->getKelasWithRelations()
        ];

        return view('admin/kelas/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kelas',
            'jurusan' => $this->jurusanModel->findAll()
        ];

        return view('admin/kelas/create', $data);
    }

    public function store()
    {
        // Validasi input dengan pengecekan manual untuk unique
        $namaKelas = $this->request->getPost('nama_kelas');
        
        // Cek nama kelas unique
        $existingKelas = $this->kelasModel->where('nama_kelas', $namaKelas)->first();
        if ($existingKelas) {
            return redirect()->back()->withInput()->with('error', 'Nama kelas sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'nama_kelas' => 'required|min_length[2]|max_length[50]',
            'jurusan_id' => 'required|numeric',
            'kapasitas' => 'required|numeric|greater_than[0]|less_than_equal_to[50]',
            'tingkat' => 'required|in_list[X,XI,XII]'
        ];

        $messages = [
            'nama_kelas' => [
                'required' => 'Nama kelas harus diisi',
                'min_length' => 'Nama kelas minimal 2 karakter',
                'max_length' => 'Nama kelas maksimal 50 karakter'
            ],
            'jurusan_id' => [
                'required' => 'Jurusan harus dipilih',
                'numeric' => 'Jurusan tidak valid'
            ],
            'kapasitas' => [
                'required' => 'Kapasitas harus diisi',
                'numeric' => 'Kapasitas harus berupa angka',
                'greater_than' => 'Kapasitas minimal 1 siswa',
                'less_than_equal_to' => 'Kapasitas maksimal 50 siswa'
            ],
            'tingkat' => [
                'required' => 'Tingkat harus dipilih',
                'in_list' => 'Tingkat tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Buat data kelas
            $kelasData = [
                'nama_kelas' => $namaKelas,
                'jurusan_id' => $this->request->getPost('jurusan_id'),
                'kapasitas' => $this->request->getPost('kapasitas'),
                'tingkat' => $this->request->getPost('tingkat'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('kelas')->insert($kelasData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kelas');
            }

            return redirect()->to('admin/kelas')->with('success', 'Kelas berhasil ditambahkan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $kelas = $this->kelasModel->getKelasWithRelations($id);
        
        if (!$kelas) {
            return redirect()->to('admin/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Kelas',
            'kelas' => $kelas,
            'jurusan' => $this->jurusanModel->findAll()
        ];

        return view('admin/kelas/edit', $data);
    }

    public function update($id = null)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            return redirect()->to('admin/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        // Validasi input dengan pengecekan manual untuk unique
        $namaKelas = $this->request->getPost('nama_kelas');
        
        // Cek nama kelas unique (kecuali untuk kelas yang sedang diedit)
        $existingKelas = $this->kelasModel->where('nama_kelas', $namaKelas)
                                         ->where('id !=', $id)
                                         ->first();
        if ($existingKelas) {
            return redirect()->back()->withInput()->with('error', 'Nama kelas sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'nama_kelas' => 'required|min_length[2]|max_length[50]',
            'jurusan_id' => 'required|numeric',
            'kapasitas' => 'required|numeric|greater_than[0]|less_than_equal_to[50]',
            'tingkat' => 'required|in_list[X,XI,XII]'
        ];

        $messages = [
            'nama_kelas' => [
                'required' => 'Nama kelas harus diisi',
                'min_length' => 'Nama kelas minimal 2 karakter',
                'max_length' => 'Nama kelas maksimal 50 karakter'
            ],
            'jurusan_id' => [
                'required' => 'Jurusan harus dipilih',
                'numeric' => 'Jurusan tidak valid'
            ],
            'kapasitas' => [
                'required' => 'Kapasitas harus diisi',
                'numeric' => 'Kapasitas harus berupa angka',
                'greater_than' => 'Kapasitas minimal 1 siswa',
                'less_than_equal_to' => 'Kapasitas maksimal 50 siswa'
            ],
            'tingkat' => [
                'required' => 'Tingkat harus dipilih',
                'in_list' => 'Tingkat tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update data kelas
            $kelasData = [
                'nama_kelas' => $namaKelas,
                'jurusan_id' => $this->request->getPost('jurusan_id'),
                'kapasitas' => $this->request->getPost('kapasitas'),
                'tingkat' => $this->request->getPost('tingkat'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('kelas')->where('id', $id)->update($kelasData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kelas');
            }

            return redirect()->to('admin/kelas')->with('success', 'Kelas berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $kelas = $this->kelasModel->find($id);
        
        if (!$kelas) {
            return redirect()->to('admin/kelas')->with('error', 'Kelas tidak ditemukan');
        }

        // Cek apakah ada siswa di kelas ini
        $siswaCount = $this->kelasModel->getSiswaCountByKelas($id);
        if ($siswaCount > 0) {
            return redirect()->to('admin/kelas')->with('error', 'Kelas tidak dapat dihapus karena masih ada siswa di dalamnya');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus data kelas
            $db->table('kelas')->where('id', $id)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('admin/kelas')->with('error', 'Gagal menghapus kelas');
            }

            return redirect()->to('admin/kelas')->with('success', 'Kelas berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/kelas')->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
} 