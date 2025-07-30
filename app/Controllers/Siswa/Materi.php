<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\MateriModel;
use App\Models\SiswaModel;

class Materi extends BaseController
{
    protected $materiModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->materiModel = new MateriModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Get siswa berdasarkan user_id
        $db = \Config\Database::connect();
        $siswa = $db->table('siswa s')
                   ->select('s.*, u.username, u.full_name, u.email, u.is_active, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, j.nama_jurusan')
                   ->join('users u', 'u.id = s.user_id')
                   ->join('kelas k', 'k.id = s.kelas_id')
                   ->join('jurusan j', 'j.id = k.jurusan_id')
                   ->where('s.user_id', $userId)
                   ->get()
                   ->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Get semua materi yang tersedia
        $materi = $this->materiModel->getAllMateri();

        $data = [
            'title' => 'Data Materi/Modul',
            'siswa' => $siswa,
            'materi' => $materi
        ];

        return view('siswa/materi/index', $data);
    }

    public function download($id = null)
    {
        $materi = $this->materiModel->find($id);
        
        if (!$materi) {
            return redirect()->to('siswa/materi')->with('error', 'Materi tidak ditemukan');
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
            return redirect()->to('siswa/materi')->with('error', 'File tidak ditemukan');
        }
    }
} 