<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriModel extends Model
{
    protected $table            = 'materi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul', 'mata_pelajaran_id', 'deskripsi', 'file_path', 'file_name', 
        'file_size', 'file_type', 'uploaded_by', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'judul' => 'required|min_length[3]|max_length[255]',
        'mata_pelajaran_id' => 'required|numeric',
        'deskripsi' => 'required|min_length[10]',
        'uploaded_by' => 'required|numeric'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];

    public function getMateriWithRelations($id = null)
    {
        $builder = $this->db->table('materi m')
                           ->select('m.*, mp.nama as nama_mata_pelajaran, u.full_name as nama_uploader')
                           ->join('mata_pelajaran mp', 'mp.id = m.mata_pelajaran_id')
                           ->join('users u', 'u.id = m.uploaded_by');

        if ($id) {
            return $builder->where('m.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('m.created_at', 'DESC')->get()->getResultArray();
    }

    public function getMateriByGuru($guruId)
    {
        return $this->db->table('materi m')
                       ->select('m.*, mp.nama as nama_mata_pelajaran, u.full_name as nama_uploader')
                       ->join('mata_pelajaran mp', 'mp.id = m.mata_pelajaran_id')
                       ->join('users u', 'u.id = m.uploaded_by')
                       ->where('m.uploaded_by', $guruId)
                       ->orderBy('m.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getAllMateri()
    {
        return $this->db->table('materi m')
                       ->select('m.*, mp.nama as nama_mata_pelajaran, u.full_name as nama_uploader')
                       ->join('mata_pelajaran mp', 'mp.id = m.mata_pelajaran_id')
                       ->join('users u', 'u.id = m.uploaded_by')
                       ->orderBy('m.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getMateriByMataPelajaran($mataPelajaranId)
    {
        return $this->db->table('materi m')
                       ->select('m.*, mp.nama as nama_mata_pelajaran, u.full_name as nama_uploader')
                       ->join('mata_pelajaran mp', 'mp.id = m.mata_pelajaran_id')
                       ->join('users u', 'u.id = m.uploaded_by')
                       ->where('m.mata_pelajaran_id', $mataPelajaranId)
                       ->orderBy('m.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getTotalMateri()
    {
        return $this->countAll();
    }

    public function getTotalMateriByGuru($guruId)
    {
        return $this->where('uploaded_by', $guruId)->countAllResults();
    }

    public function getTotalMateriByMataPelajaran($mataPelajaranId)
    {
        return $this->where('mata_pelajaran_id', $mataPelajaranId)->countAllResults();
    }

    public function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileIcon($fileType)
    {
        switch (strtolower($fileType)) {
            case 'application/pdf':
                return 'fas fa-file-pdf text-danger';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'fas fa-file-word text-primary';
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return 'fas fa-file-powerpoint text-warning';
            case 'text/plain':
                return 'fas fa-file-alt text-secondary';
            default:
                return 'fas fa-file text-muted';
        }
    }
} 