<?php

namespace App\Models;

use CodeIgniter\Model;

class PengumpulanTugasModel extends Model
{
    protected $table = 'pengumpulan_tugas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tugas_id',
        'siswa_id',
        'link_tugas',
        'catatan',
        'status',
        'submitted_at',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'tugas_id' => 'required|numeric',
        'siswa_id' => 'required|numeric',
        'link_tugas' => 'required|valid_url|max_length[500]',
        'catatan' => 'permit_empty|max_length[500]',
        'status' => 'permit_empty|in_list[submitted,late,reviewed]'
    ];

    protected $validationMessages = [
        'tugas_id' => [
            'required' => 'ID tugas harus diisi',
            'numeric' => 'ID tugas tidak valid'
        ],
        'siswa_id' => [
            'required' => 'ID siswa harus diisi',
            'numeric' => 'ID siswa tidak valid'
        ],
        'link_tugas' => [
            'required' => 'Link tugas harus diisi',
            'valid_url' => 'Format URL tidak valid',
            'max_length' => 'Link tugas maksimal 500 karakter'
        ],
        'catatan' => [
            'max_length' => 'Catatan maksimal 500 karakter'
        ],
        'status' => [
            'in_list' => 'Status tidak valid'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get pengumpulan tugas dengan relasi lengkap
     */
    public function getPengumpulanWithRelations($id = null)
    {
        $builder = $this->db->table('pengumpulan_tugas pt')
                           ->select('pt.*, t.nama_tugas, t.deadline, u.full_name as nama_siswa, s.nis,
                                   mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                           ->join('tugas t', 't.id = pt.tugas_id')
                           ->join('siswa s', 's.id = pt.siswa_id')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('jadwal j', 'j.id = t.jadwal_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->orderBy('pt.submitted_at', 'DESC');

        if ($id !== null) {
            return $builder->where('pt.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get pengumpulan tugas berdasarkan tugas ID
     */
    public function getPengumpulanByTugas($tugasId)
    {
        return $this->db->table('pengumpulan_tugas pt')
                       ->select('pt.*, u.full_name as nama_siswa, s.nis')
                       ->join('siswa s', 's.id = pt.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->where('pt.tugas_id', $tugasId)
                       ->orderBy('pt.submitted_at', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get pengumpulan tugas berdasarkan guru
     */
    public function getPengumpulanByGuru($guruId)
    {
        return $this->db->table('pengumpulan_tugas pt')
                       ->select('pt.*, t.nama_tugas, t.deadline, u.full_name as nama_siswa, s.nis,
                               mp.nama as nama_mata_pelajaran, 
                               CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                       ->join('tugas t', 't.id = pt.tugas_id')
                       ->join('siswa s', 's.id = pt.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('jadwal j', 'j.id = t.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->where('j.guru_id', $guruId)
                       ->orderBy('pt.submitted_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get pengumpulan tugas berdasarkan siswa
     */
    public function getPengumpulanBySiswa($siswaId)
    {
        return $this->getPengumpulanWithRelations()
                   ->where('pt.siswa_id', $siswaId)
                   ->get()
                   ->getResultArray();
    }

    /**
     * Check apakah siswa sudah mengumpulkan tugas
     */
    public function hasSubmitted($tugasId, $siswaId)
    {
        return $this->where('tugas_id', $tugasId)
                   ->where('siswa_id', $siswaId)
                   ->countAllResults() > 0;
    }

    /**
     * Get pengumpulan tugas berdasarkan tugas dan siswa
     */
    public function getByTugasAndSiswa($tugasId, $siswaId)
    {
        return $this->where('tugas_id', $tugasId)
                   ->where('siswa_id', $siswaId)
                   ->first();
    }

    /**
     * Update status pengumpulan
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}
