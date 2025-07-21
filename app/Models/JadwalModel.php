<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'jadwal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id', 'kelas_id', 'mata_pelajaran', 'hari', 'jam_mulai', 'jam_selesai', 
        'semester', 'tahun_ajaran', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'guru_id' => 'required|numeric',
        'kelas_id' => 'required|numeric',
        'mata_pelajaran' => 'required|min_length[2]|max_length[100]',
        'hari' => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
        'jam_mulai' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
        'jam_selesai' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
        'semester' => 'required|in_list[Ganjil,Genap]',
        'tahun_ajaran' => 'required|min_length[4]|max_length[9]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getJadwalWithRelations($id = null)
    {
        $builder = $this->db->table('jadwal j')
                           ->select('j.*, u.full_name as nama_guru, g.bidang_studi, mp.nama as nama_mata_pelajaran, k.nama_kelas, k.tingkat, jur.nama_jurusan')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users u', 'u.id = g.user_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id');

        if ($id) {
            return $builder->where('j.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getJadwalByGuru($guruId)
    {
        return $this->db->table('jadwal j')
                       ->select('j.*, mp.nama as nama_mata_pelajaran, k.nama_kelas, jr.nama_jurusan, g.full_name as nama_guru')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jr', 'jr.id = k.jurusan_id')
                       ->join('users g', 'g.id = j.guru_id')
                       ->where('j.guru_id', $guruId)
                       ->orderBy('j.hari', 'ASC')
                       ->orderBy('j.jam_mulai', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function getJadwalByGuruAndId($guruId, $jadwalId)
    {
        return $this->db->table('jadwal j')
                       ->select('j.*, mp.nama as nama_mata_pelajaran, k.nama_kelas, jr.nama_jurusan, g.full_name as nama_guru')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jr', 'jr.id = k.jurusan_id')
                       ->join('users g', 'g.id = j.guru_id')
                       ->where('j.guru_id', $guruId)
                       ->where('j.id', $jadwalId)
                       ->get()
                       ->getRowArray();
    }

    public function getJadwalByKelas($kelasId)
    {
        return $this->db->table('jadwal j')
                       ->select('j.*, mp.nama as nama_mata_pelajaran, g.full_name as nama_guru')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('users g', 'g.id = j.guru_id')
                       ->where('j.kelas_id', $kelasId)
                       ->orderBy('j.hari', 'ASC')
                       ->orderBy('j.jam_mulai', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function getJadwalByHari($hari)
    {
        return $this->db->table('jadwal j')
                       ->select('j.*, u.full_name as nama_guru, g.bidang_studi, mp.nama as nama_mata_pelajaran, k.nama_kelas, k.tingkat, jur.nama_jurusan')
                       ->join('guru g', 'g.id = j.guru_id')
                       ->join('users u', 'u.id = g.user_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('j.hari', $hari)
                       ->orderBy('j.jam_mulai', 'ASC')
                       ->get()->getResultArray();
    }

    public function checkGuruConflict($guruId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $builder = $this->db->table('jadwal')
                           ->where('guru_id', $guruId)
                           ->where('hari', $hari)
                           ->where("(
                               (jam_mulai <= '$jamMulai' AND jam_selesai > '$jamMulai') OR
                               (jam_mulai < '$jamSelesai' AND jam_selesai >= '$jamSelesai') OR
                               (jam_mulai >= '$jamMulai' AND jam_selesai <= '$jamSelesai')
                           )");

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $builder = $this->db->table('jadwal')
                           ->where('kelas_id', $kelasId)
                           ->where('hari', $hari)
                           ->where("(
                               (jam_mulai <= '$jamMulai' AND jam_selesai > '$jamMulai') OR
                               (jam_mulai < '$jamSelesai' AND jam_selesai >= '$jamSelesai') OR
                               (jam_mulai >= '$jamMulai' AND jam_selesai <= '$jamSelesai')
                           )");

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function getJadwalBySemester($semester, $tahunAjaran = null)
    {
        $builder = $this->db->table('jadwal j')
                           ->select('j.*, u.full_name as nama_guru, g.bidang_studi, mp.nama as nama_mata_pelajaran, k.nama_kelas, k.tingkat, jur.nama_jurusan')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users u', 'u.id = g.user_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id')
                           ->where('j.semester', $semester);

        if ($tahunAjaran) {
            $builder->where('j.tahun_ajaran', $tahunAjaran);
        }

        return $builder->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getTotalJadwal()
    {
        return $this->countAll();
    }

    public function getTotalJadwalByGuru($guruId)
    {
        return $this->where('guru_id', $guruId)->countAllResults();
    }

    public function getTotalJadwalByKelas($kelasId)
    {
        return $this->where('kelas_id', $kelasId)->countAllResults();
    }

    public function getTotalJadwalByHari($hari)
    {
        return $this->where('hari', $hari)->countAllResults();
    }

    public function getTotalJadwalBySemester($semester)
    {
        return $this->where('semester', $semester)->countAllResults();
    }

    public function getMataPelajaranList()
    {
        return $this->db->table('mata_pelajaran')
                       ->select('id, nama, kode')
                       ->where('status', 1)
                       ->orderBy('nama', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function getMataPelajaranByGuru($guruId)
    {
        return $this->db->table('jadwal j')
                       ->select('mp.id, mp.nama, mp.kode')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->where('j.guru_id', $guruId)
                       ->where('mp.status', 1)
                       ->distinct()
                       ->orderBy('mp.nama', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    // Legacy methods for backward compatibility
    public function getAllJadwal()
    {
        return $this->getJadwalWithRelations();
    }

    public function getJadwalById($id)
    {
        return $this->getJadwalWithRelations($id);
    }
} 