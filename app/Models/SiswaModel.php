<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'nis', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
        'alamat', 'no_telp', 'kelas_id', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|numeric',
        'nis' => 'required|min_length[8]|max_length[20]|is_unique[siswa.nis,id,{id}]',
        'jenis_kelamin' => 'required|in_list[L,P]',
        'tempat_lahir' => 'required|min_length[2]|max_length[50]',
        'tanggal_lahir' => 'required|valid_date',
        'alamat' => 'required|min_length[10]|max_length[255]',
        'no_telp' => 'required|min_length[10]|max_length[15]',
        'kelas_id' => 'required|numeric',
        'jurusan_id' => 'required|numeric'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getSiswaWithRelations($id = null)
    {
        $builder = $this->db->table('siswa s')
                           ->select('s.*, u.username, u.full_name, u.email, u.is_active, 
                                    k.nama_kelas, k.jurusan_id, j.nama_jurusan')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('kelas k', 'k.id = s.kelas_id')
                           ->join('jurusan j', 'j.id = k.jurusan_id');

        if ($id) {
            return $builder->where('s.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getSiswaByKelas($kelasId)
    {
        return $this->db->table('siswa s')
                       ->select('s.*, u.username, u.full_name, u.email')
                       ->join('users u', 'u.id = s.user_id')
                       ->where('s.kelas_id', $kelasId)
                       ->get()
                       ->getResultArray();
    }

    public function getSiswaByJurusan($jurusanId)
    {
        return $this->db->table('siswa s')
                       ->select('s.*, u.username, u.full_name, u.email')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('kelas k', 'k.id = s.kelas_id')
                       ->where('k.jurusan_id', $jurusanId)
                       ->get()
                       ->getResultArray();
    }

    public function getSiswaByNIS($nis)
    {
        return $this->db->table('siswa s')
                       ->select('s.*, u.username, u.full_name, u.email')
                       ->join('users u', 'u.id = s.user_id')
                       ->where('s.nis', $nis)
                       ->get()
                       ->getRowArray();
    }

    public function getTotalSiswa()
    {
        return $this->countAll();
    }

    public function getTotalSiswaByKelas($kelasId)
    {
        return $this->where('kelas_id', $kelasId)->countAllResults();
    }

    public function getTotalSiswaByJurusan($jurusanId)
    {
        return $this->db->table('siswa s')
                       ->join('kelas k', 'k.id = s.kelas_id')
                       ->where('k.jurusan_id', $jurusanId)
                       ->countAllResults();
    }
} 