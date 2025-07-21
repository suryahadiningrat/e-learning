<?php

namespace App\Models;

use CodeIgniter\Model;

class JawabanUlanganModel extends Model
{
    protected $table            = 'jawaban_ulangan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ulangan_id', 'siswa_id', 'jawaban_json', 'nilai', 
        'waktu_mulai', 'waktu_selesai', 'status', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'ulangan_id' => 'required|numeric',
        'siswa_id' => 'required|numeric'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getJawabanWithRelations($id = null)
    {
        $builder = $this->db->table('jawaban_ulangan ju')
                           ->select('ju.*, u.judul_ulangan, u.mata_pelajaran, s.nama_siswa, s.nis')
                           ->join('ulangan u', 'u.id = ju.ulangan_id')
                           ->join('siswa s', 's.id = ju.siswa_id');

        if ($id) {
            return $builder->where('ju.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('ju.created_at', 'DESC')->get()->getResultArray();
    }

    public function getJawabanBySiswa($siswaId)
    {
        return $this->db->table('jawaban_ulangan ju')
                       ->select('ju.*, u.judul_ulangan, u.mata_pelajaran, s.nama_siswa, s.nis')
                       ->join('ulangan u', 'u.id = ju.ulangan_id')
                       ->join('siswa s', 's.id = ju.siswa_id')
                       ->where('ju.siswa_id', $siswaId)
                       ->orderBy('ju.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getJawabanByUlangan($ulanganId)
    {
        return $this->db->table('jawaban_ulangan ju')
                       ->select('ju.*, u.judul_ulangan, u.mata_pelajaran, s.nama_siswa, s.nis')
                       ->join('ulangan u', 'u.id = ju.ulangan_id')
                       ->join('siswa s', 's.id = ju.siswa_id')
                       ->where('ju.ulangan_id', $ulanganId)
                       ->orderBy('ju.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getJawabanBySiswaAndUlangan($siswaId, $ulanganId)
    {
        return $this->db->table('jawaban_ulangan ju')
                       ->select('ju.*, u.judul_ulangan, u.mata_pelajaran, s.nama_siswa, s.nis')
                       ->join('ulangan u', 'u.id = ju.ulangan_id')
                       ->join('siswa s', 's.id = ju.siswa_id')
                       ->where('ju.siswa_id', $siswaId)
                       ->where('ju.ulangan_id', $ulanganId)
                       ->get()
                       ->getRowArray();
    }

    public function getCompletedJawabanBySiswa($siswaId)
    {
        return $this->db->table('jawaban_ulangan ju')
                       ->select('ju.*, u.judul_ulangan, u.mata_pelajaran, s.nama_siswa, s.nis')
                       ->join('ulangan u', 'u.id = ju.ulangan_id')
                       ->join('siswa s', 's.id = ju.siswa_id')
                       ->where('ju.siswa_id', $siswaId)
                       ->where('ju.status', 'completed')
                       ->orderBy('ju.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getOngoingJawabanBySiswa($siswaId)
    {
        return $this->db->table('jawaban_ulangan ju')
                       ->select('ju.*, u.judul_ulangan, u.mata_pelajaran, s.nama_siswa, s.nis')
                       ->join('ulangan u', 'u.id = ju.ulangan_id')
                       ->join('siswa s', 's.id = ju.siswa_id')
                       ->where('ju.siswa_id', $siswaId)
                       ->where('ju.status', 'ongoing')
                       ->orderBy('ju.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getTotalJawabanByUlangan($ulanganId)
    {
        return $this->where('ulangan_id', $ulanganId)->countAllResults();
    }

    public function getCompletedJawabanByUlangan($ulanganId)
    {
        return $this->where('ulangan_id', $ulanganId)
                   ->where('status', 'completed')
                   ->countAllResults();
    }

    public function getAverageNilaiByUlangan($ulanganId)
    {
        $result = $this->select('AVG(nilai) as rata_rata')
                      ->where('ulangan_id', $ulanganId)
                      ->where('status', 'completed')
                      ->get()
                      ->getRowArray();
        
        return $result ? round($result['rata_rata'], 2) : 0;
    }

    public function decodeJawabanJson($jawabanJson)
    {
        return json_decode($jawabanJson, true);
    }

    public function encodeJawabanJson($jawabanArray)
    {
        return json_encode($jawabanArray, JSON_UNESCAPED_UNICODE);
    }

    public function calculateNilai($jawabanArray, $soalArray)
    {
        $totalNilai = 0;
        $totalBobot = 0;

        foreach ($soalArray as $soal) {
            $soalId = $soal['id'];
            $bobot = isset($soal['bobot']) ? $soal['bobot'] : 10;
            $totalBobot += $bobot;

            // Cari jawaban siswa untuk soal ini
            $jawabanSiswa = null;
            foreach ($jawabanArray as $jawaban) {
                if ($jawaban['soal_id'] == $soalId) {
                    $jawabanSiswa = $jawaban['jawaban'];
                    break;
                }
            }

            if ($jawabanSiswa !== null) {
                if ($soal['tipe'] == 'pilihan_ganda') {
                    // Cek jawaban pilihan ganda
                    if ($jawabanSiswa == $soal['jawaban_benar']) {
                        $totalNilai += $bobot;
                    }
                } elseif ($soal['tipe'] == 'essay') {
                    // Untuk essay, berikan nilai berdasarkan kemiripan (simplified)
                    $similarity = $this->calculateSimilarity($jawabanSiswa, $soal['jawaban_benar']);
                    $totalNilai += ($bobot * $similarity / 100);
                }
            }
        }

        return $totalBobot > 0 ? round(($totalNilai / $totalBobot) * 100, 2) : 0;
    }

    private function calculateSimilarity($text1, $text2)
    {
        // Simplified similarity calculation
        $text1 = strtolower(trim($text1));
        $text2 = strtolower(trim($text2));
        
        if ($text1 === $text2) {
            return 100;
        }
        
        $words1 = explode(' ', $text1);
        $words2 = explode(' ', $text2);
        
        $commonWords = array_intersect($words1, $words2);
        $totalWords = count(array_unique(array_merge($words1, $words2)));
        
        return $totalWords > 0 ? round((count($commonWords) / $totalWords) * 100, 2) : 0;
    }
} 