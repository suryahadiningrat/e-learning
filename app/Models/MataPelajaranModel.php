<?php

namespace App\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table            = 'mata_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode', 'nama', 'deskripsi', 'status', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'kode' => 'required|min_length[2]|max_length[10]|is_unique[mata_pelajaran.kode,id,{id}]',
        'nama' => 'required|min_length[3]|max_length[100]',
        'status' => 'required|in_list[aktif,nonaktif]'
    ];
    protected $validationMessages   = [
        'kode' => [
            'required' => 'Kode mata pelajaran harus diisi',
            'min_length' => 'Kode minimal 2 karakter',
            'max_length' => 'Kode maksimal 10 karakter',
            'is_unique' => 'Kode mata pelajaran sudah ada'
        ],
        'nama' => [
            'required' => 'Nama mata pelajaran harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getAktifMataPelajaran()
    {
        return $this->where('status', 'aktif')
                   ->orderBy('nama', 'ASC')
                   ->findAll();
    }

    public function getMataPelajaranByKode($kode)
    {
        return $this->where('kode', $kode)->first();
    }

    public function getMataPelajaranByNama($nama)
    {
        return $this->where('nama', $nama)->first();
    }

    public function getTotalMataPelajaran()
    {
        return $this->countAll();
    }

    public function getTotalAktifMataPelajaran()
    {
        return $this->where('status', 'aktif')->countAllResults();
    }

    public function getTotalNonaktifMataPelajaran()
    {
        return $this->where('status', 'nonaktif')->countAllResults();
    }

    public function toggleStatus($id)
    {
        $mataPelajaran = $this->find($id);
        if (!$mataPelajaran) {
            return false;
        }

        $newStatus = $mataPelajaran['status'] == 'aktif' ? 'nonaktif' : 'aktif';
        return $this->update($id, ['status' => $newStatus]);
    }
} 