<?php

namespace App\Models;

use CodeIgniter\Model;

class JurusanModel extends Model
{
    protected $table            = 'jurusan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_jurusan', 'kode_jurusan', 'deskripsi'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_jurusan' => 'required|min_length[3]|max_length[100]',
        'kode_jurusan' => 'required|min_length[2]|max_length[10]|is_unique[jurusan.kode_jurusan,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_jurusan' => [
            'required' => 'Nama jurusan harus diisi',
            'min_length' => 'Nama jurusan minimal 3 karakter',
            'max_length' => 'Nama jurusan maksimal 100 karakter',
        ],
        'kode_jurusan' => [
            'required' => 'Kode jurusan harus diisi',
            'min_length' => 'Kode jurusan minimal 2 karakter',
            'max_length' => 'Kode jurusan maksimal 10 karakter',
            'is_unique' => 'Kode jurusan sudah ada',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getAllJurusan()
    {
        return $this->findAll();
    }

    public function getJurusanById($id)
    {
        return $this->find($id);
    }

    public function getJurusanWithKelas()
    {
        return $this->select('jurusan.*, COUNT(kelas.id) as jumlah_kelas')
                    ->join('kelas', 'kelas.jurusan_id = jurusan.id', 'left')
                    ->groupBy('jurusan.id')
                    ->findAll();
    }
} 