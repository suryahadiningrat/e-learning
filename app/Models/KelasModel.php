<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tingkat', 'kode_jurusan', 'paralel', 'jurusan_id', 'kapasitas', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'tingkat' => 'required|in_list[X,XI,XII]',
        'kode_jurusan' => 'required|min_length[2]|max_length[10]',
        'paralel' => 'required|alpha|max_length[1]',
        'jurusan_id' => 'required|numeric',
        'kapasitas' => 'required|numeric|greater_than[0]|less_than_equal_to[50]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getNamaKelas($tingkat, $kodeJurusan, $paralel)
    {
        return strtoupper("{$tingkat} {$kodeJurusan} {$paralel}");
    }

    public function getKelasWithRelations($id = null)
    {
        $builder = $this->db->table('kelas k')
                           ->select('k.*, j.nama_jurusan, COUNT(s.id) as jumlah_siswa, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                           ->join('jurusan j', 'j.id = k.jurusan_id')
                           ->join('siswa s', 's.kelas_id = k.id', 'left')
                           ->groupBy('k.id');

        if ($id) {
            return $builder->where('k.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getKelasByJurusan($jurusanId)
    {
        return $this->db->table('kelas k')
                       ->select('k.*, j.nama_jurusan, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                       ->join('jurusan j', 'j.id = k.jurusan_id')
                       ->where('k.jurusan_id', $jurusanId)
                       ->get()
                       ->getResultArray();
    }

    public function getKelasByTingkat($tingkat)
    {
        return $this->db->table('kelas k')
                       ->select('k.*, j.nama_jurusan, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                       ->join('jurusan j', 'j.id = k.jurusan_id')
                       ->where('k.tingkat', $tingkat)
                       ->get()
                       ->getResultArray();
    }

    public function getSiswaCountByKelas($kelasId)
    {
        return $this->db->table('siswa')
                       ->where('kelas_id', $kelasId)
                       ->countAllResults();
    }

    public function getTotalKelas()
    {
        return $this->countAll();
    }

    public function getTotalKelasByJurusan($jurusanId)
    {
        return $this->where('jurusan_id', $jurusanId)->countAllResults();
    }

    public function getTotalKelasByTingkat($tingkat)
    {
        return $this->where('tingkat', $tingkat)->countAllResults();
    }

    // Legacy methods for backward compatibility
    public function getAllKelas()
    {
        return $this->getKelasWithRelations();
    }

    public function getKelasById($id)
    {
        return $this->getKelasWithRelations($id);
    }

    public function getKelasWithSiswa()
    {
        return $this->getKelasWithRelations();
    }
} 