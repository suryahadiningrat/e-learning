<?php

namespace App\Models;

use CodeIgniter\Model;

class HariAbsensiModel extends Model
{
    protected $table            = 'hari_absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jadwal_id', 'tanggal', 'keterangan', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'jadwal_id' => 'required|numeric',
        'tanggal' => 'required|valid_date',
        'keterangan' => 'permit_empty|max_length[255]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getHariAbsensiWithRelations($id = null)
    {
        $builder = $this->db->table('hari_absensi ha')
                           ->select('ha.*, j.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, k.jurusan_id, jur.nama_jurusan, mp.nama as nama_mata_pelajaran, ug.full_name as nama_guru')
                           ->join('jadwal j', 'j.id = ha.jadwal_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users ug', 'ug.id = g.user_id');

        if ($id) {
            return $builder->where('ha.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('ha.tanggal', 'DESC')
                      ->get()->getResultArray();
    }

    public function getHariAbsensiByJadwal($jadwalId)
    {
        return $this->where('jadwal_id', $jadwalId)
                   ->orderBy('tanggal', 'DESC')
                   ->findAll();
    }

    public function getHariAbsensiByGuruAndJadwal($guruId, $jadwalId)
    {
        return $this->db->table('hari_absensi ha')
                       ->select('ha.*')
                       ->join('jadwal j', 'j.id = ha.jadwal_id')
                       ->where('j.guru_id', $guruId)
                       ->where('ha.jadwal_id', $jadwalId)
                       ->orderBy('ha.tanggal', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function checkDuplicateHari($jadwalId, $tanggal, $excludeId = null)
    {
        $builder = $this->where('jadwal_id', $jadwalId)
                       ->where('tanggal', $tanggal);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    public function getHariAbsensiForExport($startDate = null, $endDate = null, $jadwalId = null, $guruId = null)
    {
        $builder = $this->db->table('hari_absensi ha')
                           ->select('ha.*, j.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, mp.nama as nama_mata_pelajaran, ug.full_name as nama_guru')
                           ->join('jadwal j', 'j.id = ha.jadwal_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users ug', 'ug.id = g.user_id');

        // Filter by date range
        if ($startDate) {
            $builder->where('ha.tanggal >=', $startDate);
        }
        if ($endDate) {
            $builder->where('ha.tanggal <=', $endDate);
        }

        // Filter by jadwal
        if ($jadwalId) {
            $builder->where('ha.jadwal_id', $jadwalId);
        }

        // Filter by guru
        if ($guruId) {
            $builder->where('j.guru_id', $guruId);
        }

        return $builder->orderBy('ha.tanggal', 'DESC')
                      ->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getTotalHariAbsensi()
    {
        return $this->countAll();
    }

    public function getTotalHariAbsensiByJadwal($jadwalId)
    {
        return $this->where('jadwal_id', $jadwalId)->countAllResults();
    }

    public function getTotalHariAbsensiByGuru($guruId)
    {
        return $this->db->table('hari_absensi ha')
                       ->join('jadwal j', 'j.id = ha.jadwal_id')
                       ->where('j.guru_id', $guruId)
                       ->countAllResults();
    }
}