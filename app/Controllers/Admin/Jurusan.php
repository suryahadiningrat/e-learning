<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JurusanModel;

class Jurusan extends BaseController
{
    protected $jurusanModel;

    public function __construct()
    {
        $this->jurusanModel = new JurusanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Jurusan',
            'jurusan' => $this->jurusanModel->getJurusanWithKelas()
        ];

        return view('admin/jurusan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Jurusan',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/jurusan/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate($this->jurusanModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_jurusan' => $this->request->getPost('nama_jurusan'),
            'kode_jurusan' => $this->request->getPost('kode_jurusan'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        $this->jurusanModel->insert($data);

        return redirect()->to('/admin/jurusan')->with('success', 'Jurusan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jurusan = $this->jurusanModel->find($id);
        
        if (!$jurusan) {
            return redirect()->to('/admin/jurusan')->with('error', 'Jurusan tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Jurusan',
            'jurusan' => $jurusan,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/jurusan/edit', $data);
    }

    public function update($id)
    {
        $jurusan = $this->jurusanModel->find($id);
        
        if (!$jurusan) {
            return redirect()->to('/admin/jurusan')->with('error', 'Jurusan tidak ditemukan');
        }

        // Validasi input
        if (!$this->validate($this->jurusanModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_jurusan' => $this->request->getPost('nama_jurusan'),
            'kode_jurusan' => $this->request->getPost('kode_jurusan'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        $this->jurusanModel->update($id, $data);

        return redirect()->to('/admin/jurusan')->with('success', 'Jurusan berhasil diperbarui');
    }

    public function delete($id)
    {
        $jurusan = $this->jurusanModel->find($id);
        
        if (!$jurusan) {
            return redirect()->to('/admin/jurusan')->with('error', 'Jurusan tidak ditemukan');
        }

        $this->jurusanModel->delete($id);

        return redirect()->to('/admin/jurusan')->with('success', 'Jurusan berhasil dihapus');
    }
} 