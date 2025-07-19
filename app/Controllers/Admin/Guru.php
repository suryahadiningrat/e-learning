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
        $data = [
            'title' => 'Tambah Guru'
        ];

        return view('admin/guru/create', $data);
    }

    public function store()
    {
        // Validasi input dengan pengecekan manual untuk unique
        $nip = $this->request->getPost('nip');
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        
        // Cek NIP unique
        $existingNIP = $this->guruModel->where('nip', $nip)->first();
        if ($existingNIP) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan');
        }
        
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
            'nip' => 'required|min_length[8]|max_length[20]',
            'username' => 'required|min_length[3]|max_length[50]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
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
            // Buat user baru
            $userData = [
                'username' => $username,
                'full_name' => $this->request->getPost('full_name'),
                'email' => $email,
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'guru',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userId = $db->table('users')->insert($userData);

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

        $data = [
            'title' => 'Edit Guru',
            'guru' => $guru
        ];

        return view('admin/guru/edit', $data);
    }

    public function update($id = null)
    {
        $guru = $this->guruModel->find($id);
        
        if (!$guru) {
            return redirect()->to('admin/guru')->with('error', 'Guru tidak ditemukan');
        }

        // Validasi input dengan pengecekan manual untuk unique
        $nip = $this->request->getPost('nip');
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        
        // Cek NIP unique (kecuali untuk guru yang sedang diedit)
        $existingNIP = $this->guruModel->where('nip', $nip)
                                      ->where('id !=', $id)
                                      ->first();
        if ($existingNIP) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan');
        }
        
        // Cek username unique (kecuali untuk user yang sedang diedit)
        $existingUser = $this->userModel->where('username', $username)
                                       ->where('id !=', $guru['user_id'])
                                       ->first();
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan');
        }
        
        // Cek email unique (kecuali untuk user yang sedang diedit)
        $existingEmail = $this->userModel->where('email', $email)
                                        ->where('id !=', $guru['user_id'])
                                        ->first();
        if ($existingEmail) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan');
        }

        // Validasi input lainnya
        $rules = [
            'nip' => 'required|min_length[8]|max_length[20]',
            'username' => 'required|min_length[3]|max_length[50]',
            'full_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
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
            // Update user data
            $userData = [
                'username' => $username,
                'full_name' => $this->request->getPost('full_name'),
                'email' => $email,
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

            $db->table('users')->where('id', $guru['user_id'])->update($userData);

            // Update guru data
            $guruData = [
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
            // Hapus data guru
            $db->table('guru')->where('id', $id)->delete();
            
            // Hapus user terkait
            $db->table('users')->where('id', $guru['user_id'])->delete();

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
} 