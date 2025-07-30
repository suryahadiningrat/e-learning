<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\JurusanModel;
use App\Models\UserModel;

class Siswa extends BaseController
{
    protected $siswaModel;
    protected $kelasModel;
    protected $jurusanModel;
    protected $userModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->jurusanModel = new JurusanModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Siswa',
            'siswa' => $this->siswaModel->getSiswaWithRelations()
        ];

        return view('admin/siswa/index', $data);
    }

    public function create()
    {
        // Get users with role 'siswa' that haven't been assigned to any siswa yet
        $availableUsers = $this->userModel->getAvailableUsersByRole('siswa');
        
        $data = [
            'title' => 'Tambah Siswa',
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'users' => $availableUsers
        ];

        return view('admin/siswa/create', $data);
    }

    public function store()
    {
        // Validasi input
        $nis = $this->request->getPost('nis');
        $userId = $this->request->getPost('user_id');
        
        // Cek NIS unique
        $existingNIS = $this->siswaModel->where('nis', $nis)->first();
        if ($existingNIS) {
            return redirect()->back()->withInput()->with('error', 'NIS sudah digunakan');
        }
        
        // Cek user_id unique (user belum dipilih untuk siswa lain)
        $existingSiswa = $this->siswaModel->where('user_id', $userId)->first();
        if ($existingSiswa) {
            return redirect()->back()->withInput()->with('error', 'User sudah dipilih untuk siswa lain');
        }

        // Validasi input lainnya
        $rules = [
            'nis' => 'required|min_length[8]|max_length[20]',
            'user_id' => 'required|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir' => 'required|min_length[2]|max_length[50]',
            'tanggal_lahir' => 'required|valid_date',
            'alamat' => 'required|min_length[10]|max_length[255]',
            'no_telp' => 'required|min_length[10]|max_length[15]',
            'kelas_id' => 'required|numeric'
        ];

        $messages = [
            'nis' => [
                'required' => 'NIS harus diisi',
                'min_length' => 'NIS minimal 8 karakter',
                'max_length' => 'NIS maksimal 20 karakter'
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
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Buat data siswa
            $siswaData = [
                'user_id' => $userId,
                'nis' => $nis,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'alamat' => $this->request->getPost('alamat'),
                'no_telp' => $this->request->getPost('no_telp'),
                'kelas_id' => $this->request->getPost('kelas_id'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('siswa')->insert($siswaData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan siswa');
            }

            return redirect()->to('admin/siswa')->with('success', 'Siswa berhasil ditambahkan');

        } catch (\Exception $e) {
            dd($e->getMessage());
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $siswa = $this->siswaModel->getSiswaWithRelations($id);
        
        if (!$siswa) {
            return redirect()->to('admin/siswa')->with('error', 'Siswa tidak ditemukan');
        }

        // Get available users (including current user)
        $availableUsers = $this->userModel->getAvailableUsersByRole('siswa');
        
        // Add current user if not in available users (for edit case)
        $currentUser = $this->userModel->find($siswa['user_id']);
        $userExists = false;
        foreach ($availableUsers as $user) {
            if ($user['id'] == $siswa['user_id']) {
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
            'title' => 'Edit Siswa',
            'siswa' => $siswa,
            'kelas' => $this->kelasModel->getKelasWithRelations(),
            'users' => $availableUsers
        ];

        return view('admin/siswa/edit', $data);
    }

    public function update($id = null)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to('admin/siswa')->with('error', 'Siswa tidak ditemukan');
        }

        // Validasi input
        $nis = $this->request->getPost('nis');
        $userId = $this->request->getPost('user_id');
        
        // Cek NIS unique (kecuali untuk siswa yang sedang diedit)
        $existingNIS = $this->siswaModel->where('nis', $nis)
                                       ->where('id !=', $id)
                                       ->first();
        if ($existingNIS) {
            return redirect()->back()->withInput()->with('error', 'NIS sudah digunakan');
        }
        
        // Cek user_id unique (kecuali untuk siswa yang sedang diedit)
        $existingSiswa = $this->siswaModel->where('user_id', $userId)
                                         ->where('id !=', $id)
                                         ->first();
        if ($existingSiswa) {
            return redirect()->back()->withInput()->with('error', 'User sudah dipilih untuk siswa lain');
        }

        // Validasi input lainnya
        $rules = [
            'nis' => 'required|min_length[8]|max_length[20]',
            'user_id' => 'required|numeric',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir' => 'required|min_length[2]|max_length[50]',
            'tanggal_lahir' => 'required|valid_date',
            'alamat' => 'required|min_length[10]|max_length[255]',
            'no_telp' => 'required|min_length[10]|max_length[15]',
            'kelas_id' => 'required|numeric'
        ];

        $messages = [
            'nis' => [
                'required' => 'NIS harus diisi',
                'min_length' => 'NIS minimal 8 karakter',
                'max_length' => 'NIS maksimal 20 karakter'
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
            'kelas_id' => [
                'required' => 'Kelas harus dipilih',
                'numeric' => 'Kelas tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update siswa data
            $siswaData = [
                'user_id' => $userId,
                'nis' => $nis,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'alamat' => $this->request->getPost('alamat'),
                'no_telp' => $this->request->getPost('no_telp'),
                'kelas_id' => $this->request->getPost('kelas_id'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('siswa')->where('id', $id)->update($siswaData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui siswa');
            }

            return redirect()->to('admin/siswa')->with('success', 'Siswa berhasil diperbarui');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui siswa: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $siswa = $this->siswaModel->find($id);
        
        if (!$siswa) {
            return redirect()->to('admin/siswa')->with('error', 'Siswa tidak ditemukan');
        }

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus data siswa saja (user tetap ada untuk digunakan siswa lain)
            $db->table('siswa')->where('id', $id)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->to('admin/siswa')->with('error', 'Gagal menghapus siswa');
            }

            return redirect()->to('admin/siswa')->with('success', 'Siswa berhasil dihapus');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/siswa')->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }
} 