<?php
namespace App\Controllers\Siswa;
use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\SiswaModel;

class Jadwal extends BaseController
{
    public function index()
    {
        $jadwalModel = new JadwalModel();
        $siswaModel = new SiswaModel();
        $userId = session()->get('user_id');
        $siswa = $siswaModel->where('user_id', $userId)->first();
        $kelasId = $siswa['kelas_id'] ?? null;
        $jadwal = [];
        if ($kelasId) {
            $jadwal = $jadwalModel->getJadwalByKelas($kelasId);
        }
        $data = [
            'title' => 'Jadwal Siswa',
            'jadwal' => $jadwal,
            'kelas' => $siswa['kelas_id'] ?? null
        ];
        return view('siswa/jadwal/index', $data);
    }
} 