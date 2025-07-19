<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id', 'jadwal_id', 'tanggal', 'status', 'keterangan', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'siswa_id' => 'required|numeric',
        'jadwal_id' => 'required|numeric',
        'tanggal' => 'required|valid_date',
        'status' => 'required|in_list[Hadir,Sakit,Izin,Alpha]',
        'keterangan' => 'permit_empty|max_length[255]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getAbsensiWithRelations($id = null)
    {
        $builder = $this->db->table('absensi a')
                           ->select('a.*, s.nis, s.nisn, s.kelas_id, u.full_name as nama_siswa, k.nama_kelas, k.tingkat, jur.nama_jurusan, j.mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai, ug.full_name as nama_guru')
                           ->join('siswa s', 's.id = a.siswa_id')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('kelas k', 'k.id = s.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id')
                           ->join('jadwal j', 'j.id = a.jadwal_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users ug', 'ug.id = g.user_id');

        if ($id) {
            return $builder->where('a.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('a.tanggal', 'DESC')
                      ->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getAbsensiBySiswa($siswaId, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('absensi a')
                           ->select('a.*, j.mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai, ug.full_name as nama_guru')
                           ->join('jadwal j', 'j.id = a.jadwal_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users ug', 'ug.id = g.user_id')
                           ->where('a.siswa_id', $siswaId);

        if ($startDate) {
            $builder->where('a.tanggal >=', $startDate);
        }

        if ($endDate) {
            $builder->where('a.tanggal <=', $endDate);
        }

        return $builder->orderBy('a.tanggal', 'DESC')
                      ->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getAbsensiByKelas($kelasId, $tanggal = null)
    {
        $builder = $this->db->table('absensi a')
                           ->select('a.*, s.nis, s.nisn, u.full_name as nama_siswa, j.mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai, ug.full_name as nama_guru')
                           ->join('siswa s', 's.id = a.siswa_id')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('jadwal j', 'j.id = a.jadwal_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users ug', 'ug.id = g.user_id')
                           ->where('s.kelas_id', $kelasId);

        if ($tanggal) {
            $builder->where('a.tanggal', $tanggal);
        }

        return $builder->orderBy('u.full_name', 'ASC')
                      ->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getAbsensiByJadwal($jadwalId, $tanggal = null)
    {
        $builder = $this->db->table('absensi a')
                           ->select('a.*, s.nis, s.nisn, u.full_name as nama_siswa, k.nama_kelas')
                           ->join('siswa s', 's.id = a.siswa_id')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('kelas k', 'k.id = s.kelas_id')
                           ->where('a.jadwal_id', $jadwalId);

        if ($tanggal) {
            $builder->where('a.tanggal', $tanggal);
        }

        return $builder->orderBy('u.full_name', 'ASC')
                      ->get()->getResultArray();
    }

    public function getAbsensiByTanggal($tanggal)
    {
        return $this->db->table('absensi a')
                       ->select('a.*, s.nis, s.nisn, u.full_name as nama_siswa, k.nama_kelas, k.tingkat, jur.nama_jurusan, j.mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai, ug.full_name as nama_guru')
                       ->join('siswa s', 's.id = a.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('kelas k', 'k.id = s.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->join('jadwal j', 'j.id = a.jadwal_id')
                       ->join('guru g', 'g.id = j.guru_id')
                       ->join('users ug', 'ug.id = g.user_id')
                       ->where('a.tanggal', $tanggal)
                       ->orderBy('k.nama_kelas', 'ASC')
                       ->orderBy('u.full_name', 'ASC')
                       ->orderBy('j.jam_mulai', 'ASC')
                       ->get()->getResultArray();
    }

    public function checkDuplicateAbsensi($siswaId, $jadwalId, $tanggal, $excludeId = null)
    {
        $builder = $this->db->table('absensi')
                           ->where('siswa_id', $siswaId)
                           ->where('jadwal_id', $jadwalId)
                           ->where('tanggal', $tanggal);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function getAbsensiStatsBySiswa($siswaId, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('absensi')
                           ->select('status, COUNT(*) as jumlah')
                           ->where('siswa_id', $siswaId)
                           ->groupBy('status');

        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }

        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }

    public function getAbsensiStatsByKelas($kelasId, $tanggal = null)
    {
        $builder = $this->db->table('absensi a')
                           ->select('a.status, COUNT(*) as jumlah')
                           ->join('siswa s', 's.id = a.siswa_id')
                           ->where('s.kelas_id', $kelasId)
                           ->groupBy('a.status');

        if ($tanggal) {
            $builder->where('a.tanggal', $tanggal);
        }

        return $builder->get()->getResultArray();
    }

    public function getTotalAbsensi()
    {
        return $this->countAll();
    }

    public function getTotalAbsensiBySiswa($siswaId)
    {
        return $this->where('siswa_id', $siswaId)->countAllResults();
    }

    public function getTotalAbsensiByKelas($kelasId)
    {
        return $this->db->table('absensi a')
                       ->join('siswa s', 's.id = a.siswa_id')
                       ->where('s.kelas_id', $kelasId)
                       ->countAllResults();
    }

    public function getTotalAbsensiByStatus($status)
    {
        return $this->where('status', $status)->countAllResults();
    }

    public function getTotalAbsensiByTanggal($tanggal)
    {
        return $this->where('tanggal', $tanggal)->countAllResults();
    }

    // Legacy methods for backward compatibility
    public function getAllAbsensi()
    {
        return $this->getAbsensiWithRelations();
    }

    public function getAbsensiById($id)
    {
        return $this->getAbsensiWithRelations($id);
    }

    public function getAbsensiForExport($startDate = null, $endDate = null, $kelasId = null)
    {
        $builder = $this->db->table('absensi a')
                           ->select('a.*, s.nis, s.nisn, u.full_name as nama_siswa, k.nama_kelas, k.tingkat, jur.nama_jurusan, j.mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai, ug.full_name as nama_guru')
                           ->join('siswa s', 's.id = a.siswa_id')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('kelas k', 'k.id = s.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id')
                           ->join('jadwal j', 'j.id = a.jadwal_id')
                           ->join('guru g', 'g.id = j.guru_id')
                           ->join('users ug', 'ug.id = g.user_id');

        // Filter by date range
        if ($startDate) {
            $builder->where('a.tanggal >=', $startDate);
        }
        if ($endDate) {
            $builder->where('a.tanggal <=', $endDate);
        }

        // Filter by kelas
        if ($kelasId) {
            $builder->where('s.kelas_id', $kelasId);
        }

        return $builder->orderBy('a.tanggal', 'DESC')
                      ->orderBy('k.nama_kelas', 'ASC')
                      ->orderBy('u.full_name', 'ASC')
                      ->orderBy('j.hari', 'ASC')
                      ->orderBy('j.jam_mulai', 'ASC')
                      ->get()->getResultArray();
    }

    public function getAbsensiWithRelationsFiltered($startDate = null, $endDate = null, $kelasId = null)
    {
        $builder = $this->db->table('absensi a')
            ->select('a.*, s.nis, s.nisn, s.kelas_id, u.full_name as nama_siswa, k.nama_kelas, k.tingkat, jur.nama_jurusan, j.mata_pelajaran, j.hari, j.jam_mulai, j.jam_selesai, ug.full_name as nama_guru')
            ->join('siswa s', 's.id = a.siswa_id')
            ->join('users u', 'u.id = s.user_id')
            ->join('kelas k', 'k.id = s.kelas_id')
            ->join('jurusan jur', 'jur.id = k.jurusan_id')
            ->join('jadwal j', 'j.id = a.jadwal_id')
            ->join('guru g', 'g.id = j.guru_id')
            ->join('users ug', 'ug.id = g.user_id');
        if ($startDate) {
            $builder->where('a.tanggal >=', $startDate);
        }
        if ($endDate) {
            $builder->where('a.tanggal <=', $endDate);
        }
        if ($kelasId) {
            $builder->where('s.kelas_id', $kelasId);
        }
        return $builder->orderBy('a.tanggal', 'DESC')
            ->orderBy('j.hari', 'ASC')
            ->orderBy('j.jam_mulai', 'ASC')
            ->get()->getResultArray();
    }
} 