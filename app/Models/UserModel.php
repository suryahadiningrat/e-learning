<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username', 'email', 'password', 'full_name', 'photo', 'role', 'is_active', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id' => 'permit_empty|is_natural_no_zero',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
        'full_name' => 'required|min_length[3]|max_length[255]',
        'role'     => 'permit_empty|in_list[admin,guru,siswa]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = []; // Disable hashPassword callback
    protected $beforeUpdate   = []; // Disable hashPassword callback

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getInactiveUsers()
    {
        return $this->where('is_active', 0)->findAll();
    }

    public function activateUser($id)
    {
        return $this->update($id, ['is_active' => 1]);
    }

    public function deactivateUser($id)
    {
        return $this->update($id, ['is_active' => 0]);
    }

    public function getUsersByRole($role)
    {
        return $this->where('role', $role)->findAll();
    }

    public function getAvailableUsersByRole($role)
    {
        return $this->db->table('users u')
            ->select('u.id, u.username, u.full_name, u.email')
            ->where('u.role', $role)
            ->where('u.is_active', 1)
            ->whereNotIn('u.id', function($subquery) use ($role) {
                if ($role === 'siswa') {
                    $subquery->select('s.user_id')->from('siswa s');
                } elseif ($role === 'guru') {
                    $subquery->select('g.user_id')->from('guru g');
                }
            })
            ->orderBy('u.full_name', 'ASC')
            ->get()
            ->getResultArray();
    }
} 