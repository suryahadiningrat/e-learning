<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\MateriModel;
use App\Models\JadwalModel;
use App\Models\MataPelajaranModel;

class Materi extends BaseController
{
    protected $materiModel;
    protected $jadwalModel;
    protected $db;

    public function __construct()
    {
        $this->materiModel = new MateriModel();
        $this->jadwalModel = new JadwalModel();
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
        
        $data = [
            'title' => 'Data Materi/Modul',
            'materi' => $this->materiModel->getMateriByGuru($guruId)
        ];

        return view('guru/materi/index', $data);
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
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, j.hari, j.jam_mulai, j.jam_selesai')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->where('j.guru_id', $guruId)
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        $data = [
            'title' => 'Tambah Materi/Modul',
            'jadwal' => $jadwal
        ];

        return view('guru/materi/create', $data);
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
        
        // Validasi input
        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
            'jadwal_id' => 'required|numeric',
            'deskripsi' => 'required|min_length[10]',
            'file_materi' => 'uploaded[file_materi]|max_size[file_materi,10240]|ext_in[file_materi,pdf,doc,docx,ppt,pptx,txt]'
        ];

        $messages = [
            'judul' => [
                'required' => 'Judul materi harus diisi',
                'min_length' => 'Judul minimal 3 karakter',
                'max_length' => 'Judul maksimal 255 karakter'
            ],
            'jadwal_id' => [
                'required' => 'Jadwal harus dipilih',
                'numeric' => 'Jadwal tidak valid'
            ],
            'deskripsi' => [
                'required' => 'Deskripsi harus diisi',
                'min_length' => 'Deskripsi minimal 10 karakter'
            ],
            'file_materi' => [
                'uploaded' => 'File materi harus diupload',
                'max_size' => 'Ukuran file maksimal 10MB',
                'ext_in' => 'Format file harus PDF, DOC, DOCX, PPT, PPTX, atau TXT'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi jadwal (guru hanya bisa upload untuk jadwal yang diajar)
        $jadwalGuru = $this->db->table('jadwal j')
                              ->select('j.id as jadwal_id')
                              ->where('j.guru_id', $guruId)
                              ->get()
                              ->getResultArray();
        
        $jadwalIds = array_column($jadwalGuru, 'jadwal_id');
        
        if (!in_array($this->request->getPost('jadwal_id'), $jadwalIds)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak berhak mengupload materi untuk jadwal ini');
        }

        // Upload file
        $file = $this->request->getFile('file_materi');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Gunakan nama original file dengan timestamp untuk menghindari konflik
            $originalName = $file->getClientName();
            $extension = $file->getClientExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $timestamp = time();
            $newName = $fileName . '_' . $timestamp . '.' . $extension;
            
            $file->move(ROOTPATH . 'public/uploads/materi', $newName);
            
            // Simpan data materi
            $data = [
                'judul' => $this->request->getPost('judul'),
                'jadwal_id' => $this->request->getPost('jadwal_id'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'file_path' => 'uploads/materi/' . $newName,
                'file_name' => $originalName,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'uploaded_by' => session('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->materiModel->insert($data)) {
                return redirect()->to('guru/materi')->with('success', 'Materi berhasil ditambahkan');
            } else {
                // Get validation errors
                $errors = $this->materiModel->errors();
                $errorMessage = 'Gagal menambahkan materi';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', $errors);
                }
                return redirect()->back()->withInput()->with('error', $errorMessage);
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal upload file');
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
        
        $materi = $this->materiModel->getMateriWithRelations($id);
        
        if (!$materi) {
            return redirect()->to('guru/materi')->with('error', 'Materi tidak ditemukan');
        }

        // Validasi kepemilikan materi
        if ($materi['uploaded_by'] != session('user_id')) {
            return redirect()->to('guru/materi')->with('error', 'Anda tidak berhak mengedit materi ini');
        }

        // Get jadwal yang diajar oleh guru
        $jadwal = $this->db->table('jadwal j')
                          ->select('j.id as jadwal_id, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, j.hari, j.jam_mulai, j.jam_selesai')
                          ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                          ->join('kelas k', 'k.id = j.kelas_id')
                          ->where('j.guru_id', $guruId)
                          ->orderBy('mp.nama, k.tingkat, k.kode_jurusan, k.paralel')
                          ->get()
                          ->getResultArray();

        $data = [
            'title' => 'Edit Materi/Modul',
            'materi' => $materi,
            'jadwal' => $jadwal
        ];

        return view('guru/materi/edit', $data);
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
        
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('guru/materi')->with('error', 'Materi tidak ditemukan');
        }

        // Validasi kepemilikan materi
        if ($materi['uploaded_by'] != session('user_id')) {
            return redirect()->to('guru/materi')->with('error', 'Anda tidak berhak mengedit materi ini');
        }

        // Validasi input
        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
            'jadwal_id' => 'required|numeric',
            'deskripsi' => 'required|min_length[10]'
        ];

        $messages = [
            'judul' => [
                'required' => 'Judul materi harus diisi',
                'min_length' => 'Judul minimal 3 karakter',
                'max_length' => 'Judul maksimal 255 karakter'
            ],
            'jadwal_id' => [
                'required' => 'Jadwal harus dipilih',
                'numeric' => 'Jadwal tidak valid'
            ],
            'deskripsi' => [
                'required' => 'Deskripsi harus diisi',
                'min_length' => 'Deskripsi minimal 10 karakter'
            ]
        ];

        // Jika ada file baru
        $file = $this->request->getFile('file_materi');
        if ($file->isValid() && !$file->hasMoved()) {
            $rules['file_materi'] = 'uploaded[file_materi]|max_size[file_materi,10240]|ext_in[file_materi,pdf,doc,docx,ppt,pptx,txt]';
            $messages['file_materi'] = [
                'uploaded' => 'File materi harus diupload',
                'max_size' => 'Ukuran file maksimal 10MB',
                'ext_in' => 'Format file harus PDF, DOC, DOCX, PPT, PPTX, atau TXT'
            ];
        }

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
            return redirect()->back()->withInput()->with('error', 'Anda tidak berhak mengupload materi untuk jadwal ini');
        }

        // Update data
        $data = [
            'judul' => $this->request->getPost('judul'),
            'jadwal_id' => $this->request->getPost('jadwal_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Jika ada file baru
        if ($file->isValid() && !$file->hasMoved()) {
            // Hapus file lama
            if (file_exists(ROOTPATH . 'public/' . $materi['file_path'])) {
                unlink(ROOTPATH . 'public/' . $materi['file_path']);
            }

            // Upload file baru dengan nama original
            $originalName = $file->getClientName();
            $extension = $file->getClientExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $timestamp = time();
            $newName = $fileName . '_' . $timestamp . '.' . $extension;
            
            $file->move(ROOTPATH . 'public/uploads/materi', $newName);
            
            $data['file_path'] = 'uploads/materi/' . $newName;
            $data['file_name'] = $originalName;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientMimeType();
        }

        if ($this->materiModel->update($id, $data)) {
            return redirect()->to('guru/materi')->with('success', 'Materi berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui materi');
        }
    }

    public function delete($id = null)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('guru/materi')->with('error', 'Materi tidak ditemukan');
        }

        // Validasi kepemilikan materi
        if ($materi['uploaded_by'] != session('user_id')) {
            return redirect()->to('guru/materi')->with('error', 'Anda tidak berhak menghapus materi ini');
        }

        // Hapus file
        if (file_exists(ROOTPATH . 'public/' . $materi['file_path'])) {
            unlink(ROOTPATH . 'public/' . $materi['file_path']);
        }

        if ($this->materiModel->delete($id)) {
            return redirect()->to('guru/materi')->with('success', 'Materi berhasil dihapus');
        } else {
            return redirect()->to('guru/materi')->with('error', 'Gagal menghapus materi');
        }
    }

    public function download($id = null)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('guru/materi')->with('error', 'Materi tidak ditemukan');
        }

        $filePath = ROOTPATH . 'public/' . $materi['file_path'];
        
        if (file_exists($filePath)) {
            // Force download dengan header manual
            header('Content-Type: ' . $materi['file_type']);
            header('Content-Disposition: attachment; filename="' . $materi['file_name'] . '"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            // Output file content
            readfile($filePath);
            exit;
        } else {
            return redirect()->to('guru/materi')->with('error', 'File tidak ditemukan');
        }
    }
} 