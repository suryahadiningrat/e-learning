<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'nip', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
        'alamat', 'no_telp', 'bidang_studi', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|numeric',
        'nip' => 'required|min_length[8]|max_length[20]|is_unique[guru.nip,id,{id}]',
        'jenis_kelamin' => 'required|in_list[L,P]',
        'tempat_lahir' => 'required|min_length[2]|max_length[50]',
        'tanggal_lahir' => 'required|valid_date',
        'alamat' => 'required|min_length[10]|max_length[255]',
        'no_telp' => 'required|min_length[10]|max_length[15]',
        'bidang_studi' => 'required|min_length[2]|max_length[100]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getGuruWithRelations($id = null)
    {
        $builder = $this->db->table('guru g')
                           ->select('g.*, u.username, u.full_name, u.email, u.is_active')
                           ->join('users u', 'u.id = g.user_id');

        if ($id) {
            return $builder->where('g.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getGuruByBidangStudi($bidangStudi)
    {
        return $this->db->table('guru g')
                       ->select('g.*, u.username, u.full_name, u.email')
                       ->join('users u', 'u.id = g.user_id')
                       ->like('g.bidang_studi', $bidangStudi)
                       ->get()
                       ->getResultArray();
    }

    public function getGuruByNIP($nip)
    {
        return $this->db->table('guru g')
                       ->select('g.*, u.username, u.full_name, u.email')
                       ->join('users u', 'u.id = g.user_id')
                       ->where('g.nip', $nip)
                       ->get()
                       ->getRowArray();
    }

    public function getTotalGuru()
    {
        return $this->countAll();
    }

    public function getTotalGuruByBidangStudi($bidangStudi)
    {
        return $this->like('bidang_studi', $bidangStudi)->countAllResults();
    }

    // Legacy methods for backward compatibility
    public function getAllGuru()
    {
        return $this->getGuruWithRelations();
    }

    public function getGuruById($id)
    {
        return $this->getGuruWithRelations($id);
    }

    public function getGuruByUserId($user_id)
    {
        return $this->db->table('guru g')
                       ->select('g.*, u.username, u.full_name, u.email')
                       ->join('users u', 'u.id = g.user_id')
                       ->where('g.user_id', $user_id)
                       ->get()
                       ->getRowArray()['id'] ?? null;
    }
} 