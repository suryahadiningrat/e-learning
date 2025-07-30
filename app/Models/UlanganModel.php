<?php

namespace App\Models;

use CodeIgniter\Model;

class UlanganModel extends Model
{
    protected $table            = 'ulangan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul_ulangan', 'mata_pelajaran_id', 'kelas_id', 'waktu_mulai', 
        'waktu_selesai', 'durasi_menit', 'soal_json', 'created_by', 
        'status', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'judul_ulangan' => 'required|min_length[3]|max_length[255]',
        'mata_pelajaran_id' => 'required|numeric',
        'kelas_id' => 'required|numeric',
        'waktu_mulai' => 'required|valid_date',
        'waktu_selesai' => 'required|valid_date',
        'durasi_menit' => 'required|numeric|greater_than[0]',
        'created_by' => 'required|numeric'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getUlanganWithRelations($id = null)
    {
        $builder = $this->db->table('ulangan u')
                           ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, u2.full_name as nama_creator')
                           ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                           ->join('kelas k', 'k.id = u.kelas_id')
                           ->join('users u2', 'u2.id = u.created_by');

        if ($id) {
            return $builder->where('u.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('u.created_at', 'DESC')->get()->getResultArray();
    }

    public function getUlanganByCreator($userId)
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, u2.full_name as nama_creator')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('users u2', 'u2.id = u.created_by')
                       ->where('u.created_by', $userId)
                       ->orderBy('u.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getUlanganByKelas($kelasId)
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jr.nama_jurusan, us.full_name as nama_creator')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('jurusan jr', 'jr.id = k.jurusan_id')
                       ->join('users us', 'us.id = u.created_by')
                       ->where('u.kelas_id', $kelasId)
                       ->where('u.status', 'published')
                       ->orderBy('u.waktu_mulai', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getUlanganById($ulanganId)
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.id as kelas_id, jr.nama_jurusan, us.full_name as nama_creator')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('jurusan jr', 'jr.id = k.jurusan_id')
                       ->join('users us', 'us.id = u.created_by')
                       ->where('u.id', $ulanganId)
                       ->get()
                       ->getRowArray();
    }

    public function checkSiswaSudahMengerjakan($ulanganId, $siswaId)
    {
        return $this->db->table('hasil_ulangan')
                       ->where('ulangan_id', $ulanganId)
                       ->where('siswa_id', $siswaId)
                       ->countAllResults() > 0;
    }

    public function saveHasilUlangan($data)
    {
        return $this->db->table('hasil_ulangan')->insert($data);
    }

    public function getHasilUlanganSiswa($ulanganId, $siswaId)
    {
        return $this->db->table('hasil_ulangan')
                       ->where('ulangan_id', $ulanganId)
                       ->where('siswa_id', $siswaId)
                       ->get()
                       ->getRowArray();
    }

    public function getRiwayatUlanganSiswa($siswaId)
    {
        return $this->db->table('hasil_ulangan hu')
                       ->select('hu.*, u.judul_ulangan, mp.nama as nama_mata_pelajaran')
                       ->join('ulangan u', 'u.id = hu.ulangan_id')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->where('hu.siswa_id', $siswaId)
                       ->orderBy('hu.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getUlanganByMataPelajaran($mataPelajaranId)
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, u2.full_name as nama_creator')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('users u2', 'u2.id = u.created_by')
                       ->where('u.mata_pelajaran_id', $mataPelajaranId)
                       ->orderBy('u.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getPublishedUlangan()
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, u2.full_name as nama_creator')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('users u2', 'u2.id = u.created_by')
                       ->where('u.status', 'published')
                       ->orderBy('u.waktu_mulai', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function getTotalUlangan()
    {
        return $this->countAll();
    }

    public function getTotalUlanganByCreator($userId)
    {
        return $this->where('created_by', $userId)->countAllResults();
    }

    public function getTotalUlanganByKelas($kelasId)
    {
        return $this->where('kelas_id', $kelasId)->countAllResults();
    }

    public function getTotalUlanganByMataPelajaran($mataPelajaran)
    {
        return $this->where('mata_pelajaran', $mataPelajaran)->countAllResults();
    }

    public function getActiveUlangan()
    {
        $now = date('Y-m-d H:i:s');
        return $this->db->table('ulangan u')
                       ->select('u.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, u2.full_name as nama_creator')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('users u2', 'u2.id = u.created_by')
                       ->where('u.status', 'published')
                       ->where('u.waktu_mulai <=', $now)
                       ->where('u.waktu_selesai >=', $now)
                       ->orderBy('u.waktu_mulai', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function decodeSoalJson($soalJson)
    {
        return json_decode($soalJson, true);
    }

    public function encodeSoalJson($soalArray)
    {
        return json_encode($soalArray, JSON_UNESCAPED_UNICODE);
    }

    public function calculateTotalBobot($soalArray)
    {
        $totalBobot = 0;
        foreach ($soalArray as $soal) {
            $totalBobot += isset($soal['bobot']) ? $soal['bobot'] : 10;
        }
        return $totalBobot;
    }

    public function getUlanganByGuru($guruId)
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, us.full_name as nama_creator')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('users us', 'us.id = u.created_by')
                       ->where('u.created_by', $guruId)
                       ->orderBy('u.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getUlanganByGuruAndId($guruId, $ulanganId)
    {
        return $this->db->table('ulangan u')
                       ->select('u.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, COALESCE(jr.nama_jurusan, "Umum") as nama_jurusan, us.full_name as nama_creator')
                       ->join('mata_pelajaran mp', 'mp.id = u.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = u.kelas_id')
                       ->join('jurusan jr', 'jr.id = k.jurusan_id', 'left')
                       ->join('users us', 'us.id = u.created_by')
                       ->where('u.created_by', $guruId)
                       ->where('u.id', $ulanganId)
                       ->get()
                       ->getRowArray();
    }

    public function getHasilUlangan($ulanganId)
    {
        return $this->db->table('hasil_ulangan hu')
                       ->select('hu.*, s.nis, u.full_name as nama_siswa, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                       ->join('siswa s', 's.id = hu.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('kelas k', 'k.id = s.kelas_id')
                       ->where('hu.ulangan_id', $ulanganId)
                       ->orderBy('hu.nilai', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getDetailHasilUlangan($ulanganId, $siswaId)
    {
        return $this->db->table('hasil_ulangan hu')
                       ->select('hu.*, s.nis, u.full_name as nama_siswa, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                       ->join('siswa s', 's.id = hu.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('kelas k', 'k.id = s.kelas_id')
                       ->where('hu.ulangan_id', $ulanganId)
                       ->where('hu.siswa_id', $siswaId)
                       ->get()
                       ->getRowArray();
    }
} 