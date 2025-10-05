<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\UserModel;

class Guru extends BaseController
{
    protected $guruModel;
    protected $userModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Guru',
            'guru' => $this->guruModel->getGuruWithRelations()
        ];

        return view('admin/guru/index', $data);
    }

    public function create()
    {
        // Get users with role 'guru' that haven't been assigned to any guru yet
        $availableUsers = $this->userModel->getAvailableUsersByRole('guru');
        
        $data = [
            'title' => 'Tambah Guru',
            'users' => $availableUsers
        ];

        return view('admin/guru/create', $data);
    }

    public function store()
    {
        // Validasi input dengan pengecekan manual untuk unique
        $nip = $this->request->getPost('nip');
        $userId = $this->request->getPost('user_id');
        
        // Cek NIP unique
        $existingNIP = $this->guruModel->where('nip', $nip)->first();
        if ($existingNIP) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan');
        }
        
        // Cek user_id unique (user belum dipilih untuk guru lain)
        $existingGuru = $this->guruModel->where('user_id', $userId)->first();
        if ($existingGuru) {
            return redirect()->back()->withInput()->with('error', 'User sudah dipilih untuk guru lain');
        }

        // Validasi input lainnya
        $rules = [
            'nip' => 'required|min_length[8]|max_length[20]',
            'user_id' => 'required|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir' => 'required|min_length[2]|max_length[50]',
            'tanggal_lahir' => 'required|valid_date',
            'alamat' => 'required|min_length[10]|max_length[255]',
            'no_telp' => 'required|min_length[10]|max_length[15]',
            'bidang_studi' => 'required|min_length[2]|max_length[100]'
        ];

        $messages = [
            'nip' => [
                'required' => 'NIP harus diisi',
                'min_length' => 'NIP minimal 8 karakter',
                'max_length' => 'NIP maksimal 20 karakter'
            ],
            'user_id' => [
                'required' => 'User login harus dipilih',
                'numeric' => 'User login tidak valid'
            ],
            'jenis_kelamin' => [
                'required' => 'Jenis kelamin harus dipilih',
                'in_list' => 'Jenis kelamin tidak valid'
            ],
            'tempat_lahir' => [
                'required' => 'Tempat lahir harus diisi',
                'min_length' => 'Tempat lahir minimal 2 karakter',
                'max_length' => 'Tempat lahir maksimal 50 karakter'
            ],
            'tanggal_lahir' => [
                'required' => 'Tanggal lahir harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ],
            'alamat' => [
                'required' => 'Alamat harus diisi',
                'min_length' => 'Alamat minimal 10 karakter',
                'max_length' => 'Alamat maksimal 255 karakter'
            ],
            'no_telp' => [
                'required' => 'No. Telepon harus diisi',
                'min_length' => 'No. Telepon minimal 10 digit',
                'max_length' => 'No. Telepon maksimal 15 digit'
            ],
            'bidang_studi' => [
                'required' => 'Bidang studi harus diisi',
                'min_length' => 'Bidang studi minimal 2 karakter',
                'max_length' => 'Bidang studi maksimal 100 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Buat data guru
            $guruData = [
                'user_id' => $userId,
                'nip' => $nip,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'alamat' => $this->request->getPost('alamat'),
                'no_telp' => $this->request->getPost('no_telp'),
                'bidang_studi' => $this->request->getPost('bidang_studi'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('guru')->insert($guruData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan guru');
            }

            return redirect()->to('admin/guru')->with('success', 'Guru berhasil ditambahkan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan guru: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $guru = $this->guruModel->getGuruWithRelations($id);
        
        if (!$guru) {
            return redirect()->to('admin/guru')->with('error', 'Guru tidak ditemukan');
        }

        // Get available users (including current user)
        $availableUsers = $this->userModel->getAvailableUsersByRole('guru');
        
        // Add current user if not in available users (for edit case)
        $currentUser = $this->userModel->find($guru['user_id']);
        $userExists = false;
        foreach ($availableUsers as $user) {
            if ($user['id'] == $guru['user_id']) {
                $userExists = true;
                break;
            }
        }
        
        if (!$userExists && $currentUser) {
            $availableUsers[] = [
                'id' => $currentUser['id'],
                'username' => $currentUser['username'],
                'full_name' => $currentUser['full_name'],
                'email' => $currentUser['email']
            ];
        }

        $data = [
            'title' => 'Edit Guru',
            'guru' => $guru,
            'users' => $availableUsers
        ];

        return view('admin/guru/edit', $data);
    }

    public function update($id = null)
    {
        $guru = $this->guruModel->find($id);
        
        if (!$guru) {
            return redirect()->to('admin/guru')->with('error', 'Guru tidak ditemukan');
        }

        // Validasi input
        $nip = $this->request->getPost('nip');
        $userId = $this->request->getPost('user_id');
        
        // Cek NIP unique (kecuali untuk guru yang sedang diedit)
        $existingNIP = $this->guruModel->where('nip', $nip)
                                      ->where('id !=', $id)
                                      ->first();
        if ($existingNIP) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan');
        }
        
        // Cek user_id unique (kecuali untuk guru yang sedang diedit)
        $existingGuru = $this->guruModel->where('user_id', $userId)
                                       ->where('id !=', $id)
                                       ->first();
        if ($existingGuru) {
            return redirect()->back()->withInput()->with('error', 'User sudah dipilih untuk guru lain');
        }

        // Validasi input lainnya
        $rules = [
            'nip' => 'required|min_length[8]|max_length[20]',
            'user_id' => 'required|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir' => 'required|min_length[2]|max_length[50]',
            'tanggal_lahir' => 'required|valid_date',
            'alamat' => 'required|min_length[10]|max_length[255]',
            'no_telp' => 'required|min_length[10]|max_length[15]',
            'bidang_studi' => 'required|min_length[2]|max_length[100]'
        ];

        $messages = [
            'nip' => [
                'required' => 'NIP harus diisi',
                'min_length' => 'NIP minimal 8 karakter',
                'max_length' => 'NIP maksimal 20 karakter'
            ],
            'user_id' => [
                'required' => 'User login harus dipilih',
                'numeric' => 'User login tidak valid'
            ],
            'jenis_kelamin' => [
                'required' => 'Jenis kelamin harus dipilih',
                'in_list' => 'Jenis kelamin tidak valid'
            ],
            'tempat_lahir' => [
                'required' => 'Tempat lahir harus diisi',
                'min_length' => 'Tempat lahir minimal 2 karakter',
                'max_length' => 'Tempat lahir maksimal 50 karakter'
            ],
            'tanggal_lahir' => [
                'required' => 'Tanggal lahir harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ],
            'alamat' => [
                'required' => 'Alamat harus diisi',
                'min_length' => 'Alamat minimal 10 karakter',
                'max_length' => 'Alamat maksimal 255 karakter'
            ],
            'no_telp' => [
                'required' => 'No. Telepon harus diisi',
                'min_length' => 'No. Telepon minimal 10 digit',
                'max_length' => 'No. Telepon maksimal 15 digit'
            ],
            'bidang_studi' => [
                'required' => 'Bidang studi harus diisi',
                'min_length' => 'Bidang studi minimal 2 karakter',
                'max_length' => 'Bidang studi maksimal 100 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update guru data
            $guruData = [
                'user_id' => $userId,
                'nip' => $nip,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'alamat' => $this->request->getPost('alamat'),
                'no_telp' => $this->request->getPost('no_telp'),
                'bidang_studi' => $this->request->getPost('bidang_studi'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('guru')->where('id', $id)->update($guruData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui guru');
            }

            return redirect()->to('admin/guru')->with('success', 'Guru berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui guru: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $guru = $this->guruModel->find($id);
        
        if (!$guru) {
            return redirect()->to('admin/guru')->with('error', 'Guru tidak ditemukan');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus data guru saja (user tetap ada untuk digunakan guru lain)
            $db->table('guru')->where('id', $id)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('admin/guru')->with('error', 'Gagal menghapus guru');
            }

            return redirect()->to('admin/guru')->with('success', 'Guru berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/guru')->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }
    }

    public function getJadwalGuru($guruId = null)
    {
        if (!$guruId) {
            return $this->response->setJSON(['error' => 'Guru ID tidak ditemukan']);
        }

        // Get guru info
        $guru = $this->guruModel->getGuruWithRelations($guruId);
        if (!$guru) {
            return $this->response->setJSON(['error' => 'Guru tidak ditemukan']);
        }

        // Get jadwal by guru using JadwalModel method
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal j')
                    ->select('j.*, mp.nama as nama_mata_pelajaran, 
                             CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, 
                             jr.nama_jurusan, j.hari, j.jam_mulai, j.jam_selesai, j.semester, j.tahun_ajaran')
                    ->join('mata_pelajaran mp', 'mp.id = j.mata_pelajaran_id')
                    ->join('kelas k', 'k.id = j.kelas_id')
                    ->join('jurusan jr', 'jr.id = k.jurusan_id')
                    ->where('j.guru_id', $guruId)
                    ->orderBy('j.hari', 'ASC')
                    ->orderBy('j.jam_mulai', 'ASC')
                    ->get()
                    ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'guru' => $guru,
            'jadwal' => $jadwal
        ]);
    }
}