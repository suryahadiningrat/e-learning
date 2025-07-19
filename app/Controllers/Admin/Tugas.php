<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TugasModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

class Tugas extends BaseController
{
    protected $tugasModel;
    protected $guruModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->tugasModel = new TugasModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Tugas',
            'tugas' => $this->tugasModel->getTugasWithRelations()
        ];

        return view('admin/tugas/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Tugas',
            'guru' => $this->guruModel->getAllGuru(),
            'kelas' => $this->kelasModel->getAllKelas(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/tugas/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate($this->tugasModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'link_pengumpulan' => $this->request->getPost('link_pengumpulan'),
            'deadline' => $this->request->getPost('deadline'),
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        $this->tugasModel->insert($data);

        return redirect()->to('/admin/tugas')->with('success', 'Data tugas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tugas = $this->tugasModel->find($id);
        
        if (!$tugas) {
            return redirect()->to('/admin/tugas')->with('error', 'Data tugas tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Tugas',
            'tugas' => $tugas,
            'guru' => $this->guruModel->getAllGuru(),
            'kelas' => $this->kelasModel->getAllKelas(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/tugas/edit', $data);
    }

    public function update($id)
    {
        $tugas = $this->tugasModel->find($id);
        
        if (!$tugas) {
            return redirect()->to('/admin/tugas')->with('error', 'Data tugas tidak ditemukan');
        }

        // Validasi input
        if (!$this->validate($this->tugasModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'link_pengumpulan' => $this->request->getPost('link_pengumpulan'),
            'deadline' => $this->request->getPost('deadline'),
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        $this->tugasModel->update($id, $data);

        return redirect()->to('/admin/tugas')->with('success', 'Data tugas berhasil diperbarui');
    }

    public function delete($id)
    {
        $tugas = $this->tugasModel->find($id);
        
        if (!$tugas) {
            return redirect()->to('/admin/tugas')->with('error', 'Data tugas tidak ditemukan');
        }

        $this->tugasModel->delete($id);

        return redirect()->to('/admin/tugas')->with('success', 'Data tugas berhasil dihapus');
    }
} 