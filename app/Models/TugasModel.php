<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasModel extends Model
{
    protected $table = 'tugas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_tugas',
        'jadwal_id',
        'deskripsi',
        'deadline',
        'created_by',
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
        'nama_tugas' => 'required|min_length[3]|max_length[255]',
        'jadwal_id' => 'required|numeric',
        'deskripsi' => 'permit_empty|max_length[1000]',
        'deadline' => 'permit_empty|valid_date',
        'created_by' => 'required|numeric'
    ];

    protected $validationMessages = [
        'nama_tugas' => [
            'required' => 'Nama tugas harus diisi',
            'min_length' => 'Nama tugas minimal 3 karakter',
            'max_length' => 'Nama tugas maksimal 255 karakter'
        ],
        'jadwal_id' => [
            'required' => 'Jadwal harus dipilih',
            'numeric' => 'Jadwal tidak valid'
        ],
        'deskripsi' => [
            'max_length' => 'Deskripsi maksimal 1000 karakter'
        ],
        'deadline' => [
            'valid_date' => 'Format tanggal deadline tidak valid'
        ],
        'created_by' => [
            'required' => 'User pembuat harus diisi',
            'numeric' => 'User pembuat tidak valid'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get tugas dengan relasi ke jadwal, mata_pelajaran, dan kelas
     */
    public function getTugasWithRelations($id = null)
    {
        $builder = $this->db->table('tugas t')
                           ->select('t.*, j.hari, j.jam_mulai, j.jam_selesai, mp.nama as nama_mata_pelajaran, 
                                   CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas,
                                   u.full_name as nama_guru, uc.full_name as nama_pembuat')
                           ->join('jadwal j', 'j.id = t.jadwal_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users u', 'u.id = g.user_id')
                           ->join('users uc', 'uc.id = t.created_by')
                           ->orderBy('t.created_at', 'DESC');

        if ($id !== null) {
            return $builder->where('t.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get tugas berdasarkan guru yang membuat
     */
    public function getTugasByGuru($guruId)
    {
        return $this->db->table('tugas t')
                       ->select('t.*, j.hari, j.jam_mulai, j.jam_selesai, mp.nama as nama_mata_pelajaran, 
                               CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas,
                               u.full_name as nama_guru, COUNT(pt.id) as total_pengumpulan')
                       ->join('jadwal j', 'j.id = t.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('guru g', 'g.id = j.guru_id')
                       ->join('users u', 'u.id = g.user_id')
                       ->join('pengumpulan_tugas pt', 'pt.tugas_id = t.id', 'left')
                       ->where('j.guru_id', $guruId)
                       ->groupBy('t.id')
                       ->orderBy('t.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get tugas berdasarkan siswa (kelas siswa)
     */
    public function getTugasBySiswa($siswaId)
    {
        return $this->db->table('tugas t')
                       ->select('t.*, j.hari, j.jam_mulai, j.jam_selesai, mp.nama as nama_mata_pelajaran, 
                               CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas,
                               u.full_name as nama_guru,
                               pt.id as pengumpulan_id, pt.link_tugas, pt.catatan, pt.submitted_at, pt.status as status_pengumpulan')
                       ->join('jadwal j', 'j.id = t.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('guru g', 'g.id = j.guru_id')
                       ->join('users u', 'u.id = g.user_id')
                       ->join('siswa s', 's.kelas_id = k.id')
                       ->join('pengumpulan_tugas pt', 'pt.tugas_id = t.id AND pt.siswa_id = s.id', 'left')
                       ->where('s.id', $siswaId)
                       ->orderBy('t.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get semua tugas untuk admin
     */
    public function getAllTugasForAdmin()
    {
        return $this->db->table('tugas t')
                       ->select('t.*, j.hari, j.jam_mulai, j.jam_selesai, mp.nama as nama_mata_pelajaran, 
                               CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas,
                               u.full_name as nama_guru, uc.full_name as nama_pembuat, COUNT(pt.id) as total_pengumpulan')
                       ->join('jadwal j', 'j.id = t.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('guru g', 'g.id = j.guru_id')
                       ->join('users u', 'u.id = g.user_id')
                       ->join('users uc', 'uc.id = t.created_by')
                       ->join('pengumpulan_tugas pt', 'pt.tugas_id = t.id', 'left')
                       ->groupBy('t.id')
                       ->orderBy('t.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Check jika guru bisa mengelola tugas ini
     */
    public function canGuruManageTugas($tugasId, $guruId)
    {
        return $this->db->table('tugas t')
                       ->join('jadwal j', 'j.id = t.jadwal_id')
                       ->where('t.id', $tugasId)
                       ->where('j.guru_id', $guruId)
                       ->countAllResults() > 0;
    }
}
