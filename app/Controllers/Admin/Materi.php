<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MateriModel;
use App\Models\GuruModel;
use App\Models\KelasModel;

class Materi extends BaseController
{
    protected $materiModel;
    protected $guruModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->materiModel = new MateriModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Materi',
            'materi' => $this->materiModel->getMateriWithRelations()
        ];

        return view('admin/materi/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Materi',
            'guru' => $this->guruModel->getAllGuru(),
            'kelas' => $this->kelasModel->getAllKelas(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/materi/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate($this->materiModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file upload
        $file = $this->request->getFile('file_materi');
        $fileName = null;
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/materi', $fileName);
        }

        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_materi' => $fileName,
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        $this->materiModel->insert($data);

        return redirect()->to('/admin/materi')->with('success', 'Data materi berhasil ditambahkan');
    }

    public function edit($id)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('/admin/materi')->with('error', 'Data materi tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Materi',
            'materi' => $materi,
            'guru' => $this->guruModel->getAllGuru(),
            'kelas' => $this->kelasModel->getAllKelas(),
            'validation' => \Config\Services::validation()
        ];

        return view('admin/materi/edit', $data);
    }

    public function update($id)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('/admin/materi')->with('error', 'Data materi tidak ditemukan');
        }

        // Validasi input
        if (!$this->validate($this->materiModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file upload
        $file = $this->request->getFile('file_materi');
        $fileName = $materi['file_materi']; // Keep existing file if no new file uploaded
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old file if exists
            if ($materi['file_materi'] && file_exists(ROOTPATH . 'public/uploads/materi/' . $materi['file_materi'])) {
                unlink(ROOTPATH . 'public/uploads/materi/' . $materi['file_materi']);
            }
            
            $fileName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/materi', $fileName);
        }

        $data = [
            'guru_id' => $this->request->getPost('guru_id'),
            'kelas_id' => $this->request->getPost('kelas_id'),
            'judul' => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'file_materi' => $fileName,
            'semester' => $this->request->getPost('semester'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran')
        ];

        $this->materiModel->update($id, $data);

        return redirect()->to('/admin/materi')->with('success', 'Data materi berhasil diperbarui');
    }

    public function delete($id)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('/admin/materi')->with('error', 'Data materi tidak ditemukan');
        }

        // Delete file if exists
        if ($materi['file_materi'] && file_exists(ROOTPATH . 'public/uploads/materi/' . $materi['file_materi'])) {
            unlink(ROOTPATH . 'public/uploads/materi/' . $materi['file_materi']);
        }

        $this->materiModel->delete($id);

        return redirect()->to('/admin/materi')->with('success', 'Data materi berhasil dihapus');
    }

    public function download($id)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi || !$materi['file_materi']) {
            return redirect()->to('/admin/materi')->with('error', 'File tidak ditemukan');
        }

        $filePath = ROOTPATH . 'public/uploads/materi/' . $materi['file_materi'];
        
        if (!file_exists($filePath)) {
            return redirect()->to('/admin/materi')->with('error', 'File tidak ditemukan');
        }

        return $this->response->download($filePath, $materi['judul'] . '.pdf');
    }
} 