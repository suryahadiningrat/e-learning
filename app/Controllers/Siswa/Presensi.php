<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\SiswaModel;

class Presensi extends BaseController
{
    protected $absensiModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        
        // Get siswa data
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Get all absensi for this siswa
        $absensi = $this->absensiModel->getAbsensiBySiswa($siswa['id']);
        
        // Group by mata pelajaran
        $absensiByMapel = [];
        $summary = [];
        
        foreach ($absensi as $item) {
            $mapel = $item['nama_mata_pelajaran'] ?? 'Tidak diketahui';
            
            if (!isset($absensiByMapel[$mapel])) {
                $absensiByMapel[$mapel] = [];
                $summary[$mapel] = [
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'total' => 0
                ];
            }
            
            $absensiByMapel[$mapel][] = $item;
            $summary[$mapel]['total']++;
            
            $status = strtolower($item['status'] ?? '');
            if ($status == 'hadir') {
                $summary[$mapel]['hadir']++;
            } elseif ($status == 'izin') {
                $summary[$mapel]['izin']++;
            } elseif ($status == 'sakit') {
                $summary[$mapel]['sakit']++;
            } elseif ($status == 'alpha') {
                $summary[$mapel]['alpha']++;
            }
        }

        $data = [
            'title' => 'Presensi Saya',
            'siswa' => $siswa,
            'absensi' => $absensi,
            'absensiByMapel' => $absensiByMapel,
            'summary' => $summary
        ];

        return view('siswa/presensi/index', $data);
    }

    public function detail($mataPelajaran = null)
    {
        if (!$mataPelajaran) {
            return redirect()->to('siswa/presensi')->with('error', 'Mata pelajaran tidak ditemukan');
        }

        $userId = session()->get('user_id');
        
        // Get siswa data
        $siswa = $this->siswaModel->where('user_id', $userId)->first();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Decode mata pelajaran name (URL encoded)
        $mataPelajaran = urldecode($mataPelajaran);

        // Get absensi by mata pelajaran
        $db = \Config\Database::connect();
        $absensi = $db->table('absensi a')
                     ->select('a.*, ha.tanggal, ha.pertemuan_ke, mp.nama as nama_mata_pelajaran, 
                              u.full_name as nama_guru, j.hari, j.jam_mulai, j.jam_selesai')
                     ->join('hari_absensi ha', 'ha.id = a.hari_absensi_id')
                     ->join('jadwal j', 'j.id = ha.jadwal_id')
                     ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                     ->join('guru g', 'g.id = j.guru_id')
                     ->join('users u', 'u.id = g.user_id')
                     ->where('a.siswa_id', $siswa['id'])
                     ->where('mp.nama', $mataPelajaran)
                     ->orderBy('ha.tanggal', 'DESC')
                     ->get()
                     ->getResultArray();

        $data = [
            'title' => 'Detail Presensi - ' . $mataPelajaran,
            'siswa' => $siswa,
            'mataPelajaran' => $mataPelajaran,
            'absensi' => $absensi
        ];

        return view('siswa/presensi/detail', $data);
    }
}
