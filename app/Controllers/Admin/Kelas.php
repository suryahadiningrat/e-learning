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
        $tingkat = $this->request->getPost('tingkat');
        $kodeJurusan = $this->request->getPost('kode_jurusan');
        $paralel = $this->request->getPost('paralel');
        
        // Cek kombinasi kelas unique
        $existingKelas = $this->kelasModel->where([
            'tingkat' => $tingkat,
            'kode_jurusan' => $kodeJurusan,
            'paralel' => $paralel
        ])->first();
        
        if ($existingKelas) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi kelas sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'tingkat' => 'required|in_list[X,XI,XII]',
            'kode_jurusan' => 'required|min_length[2]|max_length[10]',
            'paralel' => 'required|alpha|max_length[1]',
            'jurusan_id' => 'required|numeric',
            'kapasitas' => 'required|numeric|greater_than[0]|less_than_equal_to[50]'
        ];

        $messages = [
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
                'tingkat' => $this->request->getPost('tingkat'),
                'kode_jurusan' => $this->request->getPost('kode_jurusan'),
                'paralel' => strtoupper($this->request->getPost('paralel')),
                'jurusan_id' => $this->request->getPost('jurusan_id'),
                'kapasitas' => $this->request->getPost('kapasitas'),
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
        $tingkat = $this->request->getPost('tingkat');
        $kodeJurusan = $this->request->getPost('kode_jurusan');
        $paralel = $this->request->getPost('paralel');
        
        // Cek kombinasi kelas unique (kecuali untuk kelas yang sedang diedit)
        $existingKelas = $this->kelasModel->where([
            'tingkat' => $tingkat,
            'kode_jurusan' => $kodeJurusan,
            'paralel' => $paralel
        ])->where('id !=', $id)->first();
        
        if ($existingKelas) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi kelas sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'tingkat' => 'required|in_list[X,XI,XII]',
            'kode_jurusan' => 'required|min_length[2]|max_length[10]',
            'paralel' => 'required|alpha|max_length[1]',
            'jurusan_id' => 'required|numeric',
            'kapasitas' => 'required|numeric|greater_than[0]|less_than_equal_to[50]'
        ];

        $messages = [
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
                'tingkat' => $this->request->getPost('tingkat'),
                'kode_jurusan' => $this->request->getPost('kode_jurusan'),
                'paralel' => strtoupper($this->request->getPost('paralel')),
                'jurusan_id' => $this->request->getPost('jurusan_id'),
                'kapasitas' => $this->request->getPost('kapasitas'),
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