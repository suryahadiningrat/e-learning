<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\GuruModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Get guru_id from database using user_id
        $guru = $this->guruModel->where('user_id', $userId)->first();
        
        if (!$guru) {
            return redirect()->to('guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }
        
        $guruId = $guru['id'];
        
        // Get jadwal mengajar guru
        $jadwal = $this->jadwalModel->getJadwalByGuru($guruId);
        
        $data = [
            'title' => 'Jadwal Mengajar',
            'jadwal' => $jadwal,
            'guru' => $guru
        ];

        return view('guru/jadwal/index', $data);
    }
}