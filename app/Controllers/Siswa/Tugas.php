<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\TugasModel;
use App\Models\PengumpulanTugasModel;

class Tugas extends BaseController
{
    protected $tugasModel;
    protected $pengumpulanTugasModel;
    protected $db;

    public function __construct()
    {
        $this->tugasModel = new TugasModel();
        $this->pengumpulanTugasModel = new PengumpulanTugasModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = session('user_id');
        
        // Get siswa_id from database using user_id
        $siswa = $this->db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $siswaId = $siswa['id'];

        $data = [
            'title' => 'Daftar Tugas',
            'tugas' => $this->tugasModel->getTugasBySiswa($siswaId)
        ];

        return view('siswa/tugas/index', $data);
    }

    public function detail($id = null)
    {
        $userId = session('user_id');
        
        // Get siswa_id from database using user_id
        $siswa = $this->db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $siswaId = $siswa['id'];

        $tugas = $this->tugasModel->getTugasWithRelations($id);

        if (!$tugas) {
            return redirect()->to('siswa/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Check if siswa belongs to the class of this task
        $isInClass = $this->db->table('jadwal j')
                             ->join('kelas k', 'k.id = j.kelas_id')
                             ->join('siswa s', 's.kelas_id = k.id')
                             ->where('j.id', $tugas['jadwal_id'])
                             ->where('s.id', $siswaId)
                             ->countAllResults() > 0;

        if (!$isInClass) {
            return redirect()->to('siswa/tugas')->with('error', 'Anda tidak berhak mengakses tugas ini');
        }

        // Get pengumpulan siswa untuk tugas ini
        $pengumpulan = $this->pengumpulanTugasModel->getByTugasAndSiswa($id, $siswaId);

        $data = [
            'title' => 'Detail Tugas',
            'tugas' => $tugas,
            'pengumpulan' => $pengumpulan
        ];

        return view('siswa/tugas/detail', $data);
    }

    public function submit($id = null)
    {
        $id = $this->request->getPost('tugas_id');
        $userId = session('user_id');
        
        // Get siswa_id from database using user_id
        $siswa = $this->db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $siswaId = $siswa['id'];

        $tugas = $this->tugasModel->getTugasWithRelations($id);

        if (!$tugas) {
            return redirect()->to('siswa/tugas')->with('error', 'Tugas tidak ditemukan');
        }

        // Check if siswa belongs to the class of this task
        $isInClass = $this->db->table('jadwal j')
                             ->join('kelas k', 'k.id = j.kelas_id')
                             ->join('siswa s', 's.kelas_id = k.id')
                             ->where('j.id', $tugas['jadwal_id'])
                             ->where('s.id', $siswaId)
                             ->countAllResults() > 0;

        if (!$isInClass) {
            return redirect()->to('siswa/tugas')->with('error', 'Anda tidak berhak mengumpulkan tugas ini');
        }

        // Check if already submitted
        if ($this->pengumpulanTugasModel->hasSubmitted($id, $siswaId)) {
            return redirect()->to("siswa/tugas/detail/$id")->with('error', 'Anda sudah mengumpulkan tugas ini');
        }

        // Validate input
        $rules = [
            'link_tugas' => 'required|valid_url|max_length[500]',
            'catatan' => 'permit_empty|max_length[500]'
        ];

        $messages = [
            'link_tugas' => [
                'required' => 'Link tugas harus diisi',
                'valid_url' => 'Format URL tidak valid',
                'max_length' => 'Link tugas maksimal 500 karakter'
            ],
            'catatan' => [
                'max_length' => 'Catatan maksimal 500 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if past deadline
        $status = 'submitted';
        if ($tugas['deadline'] && strtotime($tugas['deadline']) < time()) {
            $status = 'late';
        }

        $data = [
            'tugas_id' => $id,
            'siswa_id' => $siswaId,
            'link_tugas' => $this->request->getPost('link_tugas'),
            'catatan' => $this->request->getPost('catatan'),
            'status' => $status,
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pengumpulanTugasModel->insert($data)) {
            return redirect()->to("siswa/tugas/detail/$id")->with('success', 'Tugas berhasil dikumpulkan');
        } else {
            $errors = $this->pengumpulanTugasModel->errors();
            return redirect()->back()->withInput()->with('error', 'Gagal mengumpulkan tugas: ' . implode(', ', $errors));
        }
    }

    public function editSubmission($id = null)
    {
        $userId = session('user_id');
        
        // Get siswa_id from database using user_id
        $siswa = $this->db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $siswaId = $siswa['id'];

        $pengumpulan = $this->pengumpulanTugasModel->find($id);

        if (!$pengumpulan) {
            return redirect()->to('siswa/tugas')->with('error', 'Pengumpulan tidak ditemukan');
        }

        // Validate ownership
        if ($pengumpulan['siswa_id'] != $siswaId) {
            return redirect()->to('siswa/tugas')->with('error', 'Anda tidak berhak mengedit pengumpulan ini');
        }

        // Validate input
        $rules = [
            'link_tugas' => 'required|valid_url|max_length[500]',
            'catatan' => 'permit_empty|max_length[500]'
        ];

        $messages = [
            'link_tugas' => [
                'required' => 'Link tugas harus diisi',
                'valid_url' => 'Format URL tidak valid',
                'max_length' => 'Link tugas maksimal 500 karakter'
            ],
            'catatan' => [
                'max_length' => 'Catatan maksimal 500 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get tugas info for deadline check
        $tugas = $this->tugasModel->getTugasWithRelations($pengumpulan['tugas_id']);
        
        // Update status if editing after deadline
        $status = $pengumpulan['status'];
        if ($tugas['deadline'] && strtotime($tugas['deadline']) < time() && $status == 'submitted') {
            $status = 'late';
        }

        $data = [
            'link_tugas' => $this->request->getPost('link_tugas'),
            'catatan' => $this->request->getPost('catatan'),
            'status' => $status,
            'submitted_at' => date('Y-m-d H:i:s') // Update submission time
        ];

        if ($this->pengumpulanTugasModel->update($id, $data)) {
            return redirect()->to("siswa/tugas/detail/{$pengumpulan['tugas_id']}")->with('success', 'Pengumpulan tugas berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengumpulan tugas');
        }
    }

    public function deleteSubmission($id = null)
    {
        $userId = session('user_id');
        
        // Get siswa_id from database using user_id
        $siswa = $this->db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$siswa) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data siswa tidak ditemukan'
            ]);
        }
        
        $siswaId = $siswa['id'];

        $pengumpulan = $this->pengumpulanTugasModel->find($id);

        if (!$pengumpulan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengumpulan tidak ditemukan'
            ]);
        }

        // Validate ownership
        if ($pengumpulan['siswa_id'] != $siswaId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak berhak menghapus pengumpulan ini'
            ]);
        }

        if ($this->pengumpulanTugasModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pengumpulan berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus pengumpulan'
            ]);
        }
    }

    public function update()
    {
        $userId = session('user_id');
        
        // Get siswa_id from database using user_id
        $siswa = $this->db->table('siswa')->where('user_id', $userId)->get()->getRowArray();
        
        if (!$siswa) {
            return redirect()->to('siswa/dashboard')->with('error', 'Data siswa tidak ditemukan');
        }
        
        $siswaId = $siswa['id'];
        $pengumpulanId = $this->request->getPost('pengumpulan_id');

        $pengumpulan = $this->pengumpulanTugasModel->find($pengumpulanId);

        if (!$pengumpulan) {
            return redirect()->to('siswa/tugas')->with('error', 'Pengumpulan tidak ditemukan');
        }

        // Validate ownership
        if ($pengumpulan['siswa_id'] != $siswaId) {
            return redirect()->to('siswa/tugas')->with('error', 'Anda tidak berhak mengedit pengumpulan ini');
        }

        // Validate input
        $rules = [
            'link_tugas' => 'required|valid_url|max_length[500]',
            'keterangan' => 'permit_empty|max_length[500]'
        ];

        $messages = [
            'link_tugas' => [
                'required' => 'Link tugas harus diisi',
                'valid_url' => 'Format URL tidak valid',
                'max_length' => 'Link tugas maksimal 500 karakter'
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 500 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get tugas info for deadline check
        $tugas = $this->tugasModel->getTugasWithRelations($pengumpulan['tugas_id']);
        
        // Update status if editing after deadline
        $status = $pengumpulan['status'];
        if ($tugas['deadline'] && strtotime($tugas['deadline']) < time() && $status == 'submitted') {
            $status = 'late';
        }

        $data = [
            'link_tugas' => $this->request->getPost('link_tugas'),
            'catatan' => $this->request->getPost('keterangan'),
            'status' => $status,
            'submitted_at' => date('Y-m-d H:i:s') // Update submission time
        ];

        if ($this->pengumpulanTugasModel->update($pengumpulanId, $data)) {
            return redirect()->to("siswa/tugas/detail/{$pengumpulan['tugas_id']}")->with('success', 'Pengumpulan tugas berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengumpulan tugas');
        }
    }
}
