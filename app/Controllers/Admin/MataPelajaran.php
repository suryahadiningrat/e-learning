<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataPelajaranModel;

class MataPelajaran extends BaseController
{
    protected $mataPelajaranModel;

    public function __construct()
    {
        $this->mataPelajaranModel = new MataPelajaranModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Mata Pelajaran',
            'mata_pelajaran' => $this->mataPelajaranModel->findAll()
        ];

        return view('admin/mata_pelajaran/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Mata Pelajaran'
        ];

        return view('admin/mata_pelajaran/create', $data);
    }

    public function store()
    {
        // Validasi input
        $rules = [
            'kode' => 'required|min_length[2]|max_length[10]|is_unique[mata_pelajaran.kode]',
            'nama' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[aktif,nonaktif]'
        ];

        $messages = [
            'kode' => [
                'required' => 'Kode mata pelajaran harus diisi',
                'min_length' => 'Kode minimal 2 karakter',
                'max_length' => 'Kode maksimal 10 karakter',
                'is_unique' => 'Kode mata pelajaran sudah ada'
            ],
            'nama' => [
                'required' => 'Nama mata pelajaran harus diisi',
                'min_length' => 'Nama minimal 3 karakter',
                'max_length' => 'Nama maksimal 100 karakter'
            ],
            'deskripsi' => [
                'max_length' => 'Deskripsi maksimal 500 karakter'
            ],
            'status' => [
                'required' => 'Status harus dipilih',
                'in_list' => 'Status tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan data mata pelajaran
        $data = [
            'kode' => strtoupper($this->request->getPost('kode')),
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => $this->request->getPost('status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->mataPelajaranModel->insert($data)) {
            return redirect()->to('admin/mata-pelajaran')->with('success', 'Mata pelajaran berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan mata pelajaran');
        }
    }

    public function edit($id = null)
    {
        $mataPelajaran = $this->mataPelajaranModel->find($id);
        
        if (!$mataPelajaran) {
            return redirect()->to('admin/mata-pelajaran')->with('error', 'Mata pelajaran tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Mata Pelajaran',
            'mata_pelajaran' => $mataPelajaran
        ];

        return view('admin/mata_pelajaran/edit', $data);
    }

    public function update($id = null)
    {
        $mataPelajaran = $this->mataPelajaranModel->find($id);
        
        if (!$mataPelajaran) {
            return redirect()->to('admin/mata-pelajaran')->with('error', 'Mata pelajaran tidak ditemukan');
        }

        // Validasi input
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'deskripsi' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[aktif,nonaktif]'
        ];

        $messages = [
            'nama' => [
                'required' => 'Nama mata pelajaran harus diisi',
                'min_length' => 'Nama minimal 3 karakter',
                'max_length' => 'Nama maksimal 100 karakter'
            ],
            'deskripsi' => [
                'max_length' => 'Deskripsi maksimal 500 karakter'
            ],
            'status' => [
                'required' => 'Status harus dipilih',
                'in_list' => 'Status tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update data mata pelajaran
        $data = [
            'nama' => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->mataPelajaranModel->update($id, $data)) {
            return redirect()->to('admin/mata-pelajaran')->with('success', 'Mata pelajaran berhasil diupdate');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate mata pelajaran');
        }
    }

    public function delete($id = null)
    {
        $mataPelajaran = $this->mataPelajaranModel->find($id);
        
        if (!$mataPelajaran) {
            return redirect()->to('admin/mata-pelajaran')->with('error', 'Mata pelajaran tidak ditemukan');
        }

        if ($this->mataPelajaranModel->delete($id)) {
            return redirect()->to('admin/mata-pelajaran')->with('success', 'Mata pelajaran berhasil dihapus');
        } else {
            return redirect()->to('admin/mata-pelajaran')->with('error', 'Gagal menghapus mata pelajaran');
        }
    }

    public function toggleStatus($id = null)
    {
        $mataPelajaran = $this->mataPelajaranModel->find($id);
        
        if (!$mataPelajaran) {
            return redirect()->to('admin/mata-pelajaran')->with('error', 'Mata pelajaran tidak ditemukan');
        }

        if ($this->mataPelajaranModel->toggleStatus($id)) {
            $newStatus = $mataPelajaran['status'] == 'aktif' ? 'dinonaktifkan' : 'diaktifkan';
            return redirect()->to('admin/mata-pelajaran')->with('success', "Mata pelajaran berhasil $newStatus");
        } else {
            return redirect()->to('admin/mata-pelajaran')->with('error', 'Gagal mengubah status mata pelajaran');
        }
    }
} 