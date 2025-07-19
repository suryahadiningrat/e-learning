<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\SiswaModel;
use App\Models\JadwalModel;

class Nilai extends BaseController
{
    protected $nilaiModel;
    protected $siswaModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
        $this->siswaModel = new SiswaModel();
        $this->jadwalModel = new JadwalModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Nilai',
            'nilai' => $this->nilaiModel->getNilaiWithRelations()
        ];

        return view('admin/nilai/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Nilai',
            'siswa' => $this->siswaModel->getAllSiswa(),
            'jadwal' => $this->jadwalModel->getAllJadwal(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/nilai/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate($this->nilaiModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'siswa_id' => $this->request->getPost('siswa_id'),
            'jadwal_id' => $this->request->getPost('jadwal_id'),
            'nilai_tugas' => $this->request->getPost('nilai_tugas'),
            'nilai_uts' => $this->request->getPost('nilai_uts'),
            'nilai_uas' => $this->request->getPost('nilai_uas'),
            'nilai_akhir' => $this->request->getPost('nilai_akhir'),
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        $this->nilaiModel->insert($data);

        return redirect()->to('/admin/nilai')->with('success', 'Data nilai berhasil ditambahkan');
    }

    public function edit($id)
    {
        $nilai = $this->nilaiModel->find($id);
        
        if (!$nilai) {
            return redirect()->to('/admin/nilai')->with('error', 'Data nilai tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Nilai',
            'nilai' => $nilai,
            'siswa' => $this->siswaModel->getAllSiswa(),
            'jadwal' => $this->jadwalModel->getAllJadwal(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/nilai/edit', $data);
    }

    public function update($id)
    {
        $nilai = $this->nilaiModel->find($id);
        
        if (!$nilai) {
            return redirect()->to('/admin/nilai')->with('error', 'Data nilai tidak ditemukan');
        }

        // Validasi input
        if (!$this->validate($this->nilaiModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'siswa_id' => $this->request->getPost('siswa_id'),
            'jadwal_id' => $this->request->getPost('jadwal_id'),
            'nilai_tugas' => $this->request->getPost('nilai_tugas'),
            'nilai_uts' => $this->request->getPost('nilai_uts'),
            'nilai_uas' => $this->request->getPost('nilai_uas'),
            'nilai_akhir' => $this->request->getPost('nilai_akhir'),
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        $this->nilaiModel->update($id, $data);

        return redirect()->to('/admin/nilai')->with('success', 'Data nilai berhasil diperbarui');
    }

    public function delete($id)
    {
        $nilai = $this->nilaiModel->find($id);
        
        if (!$nilai) {
            return redirect()->to('/admin/nilai')->with('error', 'Data nilai tidak ditemukan');
        }

        $this->nilaiModel->delete($id);

        return redirect()->to('/admin/nilai')->with('success', 'Data nilai berhasil dihapus');
    }
} 