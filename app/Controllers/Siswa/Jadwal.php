<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\SiswaModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Get siswa data
        $siswa = $this->siswaModel->getSiswaByUserId($userId);
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Get jadwal berdasarkan kelas siswa
        $jadwal = $this->jadwalModel->getJadwalByKelas($siswa['kelas_id']);
        
        $data = [
            'title' => 'Jadwal Pelajaran',
            'jadwal' => $jadwal,
            'siswa' => $siswa
        ];

        return view('siswa/jadwal/index', $data);
    }
} 