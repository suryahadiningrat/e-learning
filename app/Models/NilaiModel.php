<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table            = 'nilai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id', 'jadwal_id', 'nilai_tugas', 'nilai_ulangan', 
        'nilai_uts', 'nilai_uas', 'created_at', 'updated_at'
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
        'nilai_uts' => 'permit_empty|decimal',
        'nilai_uas' => 'permit_empty|decimal'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['prepareJsonFields'];
    protected $beforeUpdate   = ['prepareJsonFields'];

    protected function prepareJsonFields(array $data)
    {
        if (isset($data['data']['nilai_tugas']) && is_array($data['data']['nilai_tugas'])) {
            $data['data']['nilai_tugas'] = json_encode($data['data']['nilai_tugas']);
        }
        
        if (isset($data['data']['nilai_ulangan']) && is_array($data['data']['nilai_ulangan'])) {
            $data['data']['nilai_ulangan'] = json_encode($data['data']['nilai_ulangan']);
        }
        
        return $data;
    }

    public function getNilaiWithRelations($jadwalId = null)
    {
        $builder = $this->db->table('nilai n')
                           ->select('n.*, s.nis, u.full_name as nama_siswa, 
                                    mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
                           ->join('siswa s', 's.id = n.siswa_id')
                           ->join('users u', 'u.id = s.user_id')
                           ->join('jadwal j', 'j.id = n.jadwal_id')
                           ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                           ->join('kelas k', 'k.id = j.kelas_id')
                           ->join('jurusan jur', 'jur.id = k.jurusan_id');

        if ($jadwalId) {
            $builder->where('n.jadwal_id', $jadwalId);
        }

        return $builder->get()->getResultArray();
    }

    public function getNilaiByJadwal($jadwalId)
    {
        return $this->getNilaiWithRelations($jadwalId);
    }

    public function getNilaiBySiswa($siswaId)
    {
        return $this->db->table('nilai n')
                       ->select('n.*, mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
                       ->join('jadwal j', 'j.id = n.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('n.siswa_id', $siswaId)
                       ->get()
                       ->getResultArray();
    }

    public function getNilaiByJurusan($jurusanId)
    {
        return $this->db->table('nilai n')
                       ->select('n.*, s.nis, u.full_name as nama_siswa, 
                                mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
                       ->join('siswa s', 's.id = n.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('jadwal j', 'j.id = n.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('jur.id', $jurusanId)
                       ->get()
                       ->getResultArray();
    }

    public function getMataPelajaranByJurusan($jurusanId)
    {
        return $this->db->table('jadwal j')
                       ->select('j.id, mp.nama as nama_mata_pelajaran, j.kelas_id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, jur.id as jurusan_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('jur.id', $jurusanId)
                       ->groupBy('j.id, mp.nama, j.kelas_id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, jur.id')
                       ->get()
                       ->getResultArray();
    }

    public function getSiswaByJadwal($jadwalId)
    {
        return $this->db->table('siswa s')
                       ->select('s.*, u.full_name as nama_siswa, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('kelas k', 'k.id = s.kelas_id')
                       ->join('jadwal j', 'j.kelas_id = s.kelas_id')
                       ->where('j.id', $jadwalId)
                       ->get()
                       ->getResultArray();
    }

    public function saveNilaiBatch($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($data as $nilai) {
                // Cek apakah nilai sudah ada
                $existing = $this->where('siswa_id', $nilai['siswa_id'])
                                ->where('jadwal_id', $nilai['jadwal_id'])
                                ->first();

                if ($existing) {
                    // Update nilai yang sudah ada
                    $this->update($existing['id'], $nilai);
                } else {
                    // Insert nilai baru
                    $this->insert($nilai);
                }
            }

            $db->transComplete();
            return $db->transStatus() !== false;

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function getJurusanByGuru($guruId)
    {
        return $this->db->table('jadwal j')
                       ->select('jur.id, jur.nama_jurusan, COUNT(DISTINCT j.id) as jumlah_mapel')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('j.guru_id', $guruId)
                       ->groupBy('jur.id, jur.nama_jurusan')
                       ->get()
                       ->getResultArray();
    }

    public function getMataPelajaranByGuruAndJurusan($guruId, $jurusanId)
    {
        return $this->db->table('jadwal j')
                       ->select('j.id, mp.nama as nama_mata_pelajaran, j.kelas_id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, jur.id as jurusan_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('j.guru_id', $guruId)
                       ->where('jur.id', $jurusanId)
                       ->groupBy('j.id, mp.nama, j.kelas_id, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan, jur.id')
                       ->get()
                       ->getResultArray();
    }

    public function getNilaiByGuruAndJurusan($guruId, $jurusanId)
    {
        return $this->db->table('nilai n')
                       ->select('n.*, s.nis, u.full_name as nama_siswa, 
                                mp.nama as nama_mata_pelajaran, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
                       ->join('siswa s', 's.id = n.siswa_id')
                       ->join('users u', 'u.id = s.user_id')
                       ->join('jadwal j', 'j.id = n.jadwal_id')
                       ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                       ->join('kelas k', 'k.id = j.kelas_id')
                       ->join('jurusan jur', 'jur.id = k.jurusan_id')
                       ->where('j.guru_id', $guruId)
                       ->where('jur.id', $jurusanId)
                       ->get()
                       ->getResultArray();
    }
} 