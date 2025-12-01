<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;

class UserPengguna extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $data = [
            'title' => 'User Pengguna',
            'users' => $this->userModel->findAll()
        ];

        return view('admin/user_pengguna/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah User Pengguna'
        ];

        return view('admin/user_pengguna/create', $data);
    }

    public function store()
    {
        // Validasi input dengan pengecekan manual untuk unique
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        
        // Cek username unique
        $existingUser = $this->userModel->where('username', $username)->first();
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan');
        }
        
        // Cek email unique
        $existingEmail = $this->userModel->where('email', $email)->first();
        if ($existingEmail) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'role' => 'required|in_list[admin,guru,siswa]'
        ];

        $messages = [
            'username' => [
                'required' => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'max_length' => 'Username maksimal 50 karakter'
            ],
            'full_name' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 100 karakter'
            ],
            'email' => [
                'required' => 'Email harus diisi',
                'valid_email' => 'Format email tidak valid'
            ],
            'password' => [
                'required' => 'Password harus diisi',
                'min_length' => 'Password minimal 6 karakter'
            ],
            'confirm_password' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches' => 'Konfirmasi password tidak cocok'
            ],
            'role' => [
                'required' => 'Role harus dipilih',
                'in_list' => 'Role tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan user baru
        $userData = [
            'username' => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Gunakan query builder langsung untuk memastikan insert berfungsi
            $db = \Config\Database::connect();
            $result = $db->table('users')->insert($userData);
            
            if ($result) {
                return redirect()->to('admin/user-pengguna')->with('success', 'User berhasil ditambahkan');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('admin/user-pengguna')->with('error', 'User tidak ditemukan');
        }

        // Get guru/siswa data if exists
        $guru = null;
        $siswa = null;
        $kelas = [];
        
        if ($user['role'] == 'guru') {
            $guru = $this->guruModel->where('user_id', $id)->first();
        } elseif ($user['role'] == 'siswa') {
            $siswa = $this->siswaModel->where('user_id', $id)->first();
            $kelas = $this->kelasModel->findAll();
        }

        $data = [
            'title' => 'Edit User Pengguna',
            'user' => $user,
            'guru' => $guru,
            'siswa' => $siswa,
            'kelas' => $kelas
        ];

        return view('admin/user_pengguna/edit', $data);
    }

    public function update($id = null)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('admin/user-pengguna')->with('error', 'User tidak ditemukan');
        }

        // Validasi input dengan pengecekan manual untuk unique
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        
        // Cek username unique (kecuali untuk user yang sedang diedit)
        $existingUser = $this->userModel->where('username', $username)
                                       ->where('id !=', $id)
                                       ->first();
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan');
        }
        
        // Cek email unique (kecuali untuk user yang sedang diedit)
        $existingEmail = $this->userModel->where('email', $email)
                                        ->where('id !=', $id)
                                        ->first();
        if ($existingEmail) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[admin,guru,siswa]'
        ];

        $messages = [
            'username' => [
                'required' => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'max_length' => 'Username maksimal 50 karakter'
            ],
            'full_name' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 100 karakter'
            ],
            'email' => [
                'required' => 'Email harus diisi',
                'valid_email' => 'Format email tidak valid'
            ],
            'role' => [
                'required' => 'Role harus dipilih',
                'in_list' => 'Role tidak valid'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update user data
        $userData = [
            'username' => $username,
            'full_name' => $this->request->getPost('full_name'),
            'email' => $email,
            'role' => $this->request->getPost('role'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update password jika diisi
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $confirmPassword = $this->request->getPost('confirm_password');
            if ($password !== $confirmPassword) {
                return redirect()->back()->withInput()->with('error', 'Konfirmasi password tidak cocok');
            }
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        try {
            // Gunakan query builder langsung untuk memastikan update berfungsi
            $db = \Config\Database::connect();
            $result = $db->table('users')
                        ->where('id', $id)
                        ->update($userData);
            
            // Update guru/siswa data based on role
            $role = $this->request->getPost('role');
            
            if ($role == 'guru') {
                $guruData = [
                    'user_id' => $id,
                    'nip' => $this->request->getPost('nip') ?: '',
                    'bidang_studi' => $this->request->getPost('bidang_studi') ?: '',
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: 'L',
                    'no_telp' => $this->request->getPost('no_telp') ?: '',
                    'tempat_lahir' => $this->request->getPost('tempat_lahir') ?: '',
                    'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
                    'alamat' => $this->request->getPost('alamat') ?: ''
                ];
                
                $existingGuru = $this->guruModel->where('user_id', $id)->first();
                if ($existingGuru) {
                    $db->table('guru')->where('user_id', $id)->update($guruData);
                } else {
                    $db->table('guru')->insert($guruData);
                }
            } elseif ($role == 'siswa') {
                $siswaData = [
                    'user_id' => $id,
                    'nis' => $this->request->getPost('nis') ?: '',
                    'kelas_id' => $this->request->getPost('kelas_id') ?: null,
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: 'L',
                    'no_telp' => $this->request->getPost('no_telp') ?: '',
                    'tempat_lahir' => $this->request->getPost('tempat_lahir') ?: '',
                    'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
                    'alamat' => $this->request->getPost('alamat') ?: ''
                ];
                
                $existingSiswa = $this->siswaModel->where('user_id', $id)->first();
                if ($existingSiswa) {
                    $db->table('siswa')->where('user_id', $id)->update($siswaData);
                } else {
                    $db->table('siswa')->insert($siswaData);
                }
            }
            
            if ($result) {
                return redirect()->to('admin/user-pengguna')->with('success', 'User berhasil diperbarui');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('admin/user-pengguna')->with('error', 'User tidak ditemukan');
        }

        // Cek apakah user yang sedang login
        if ($id == session()->get('user_id')) {
            return redirect()->to('admin/user-pengguna')->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        try {
            $this->userModel->delete($id);
            return redirect()->to('admin/user-pengguna')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('admin/user-pengguna')->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id = null)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('admin/user-pengguna')->with('error', 'User tidak ditemukan');
        }

        // Cek apakah user yang sedang login
        if ($id == session()->get('user_id')) {
            return redirect()->to('admin/user-pengguna')->with('error', 'Tidak dapat mengubah status akun sendiri');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        try {
            $this->userModel->update($id, ['is_active' => $newStatus]);
            return redirect()->to('admin/user-pengguna')->with('success', "User berhasil $statusText");
        } catch (\Exception $e) {
            return redirect()->to('admin/user-pengguna')->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }
} 