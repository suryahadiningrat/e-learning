<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        // Redirect to login if not logged in
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectUrl());
        }
        
        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0) {
                session()->setFlashdata('error', 'Akun Anda belum diaktivasi. Silakan hubungi admin.');
                return redirect()->back();
            }

            // Set session
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'logged_in' => true
            ]);

            return redirect()->to($this->getRedirectUrl());
        } else {
            session()->setFlashdata('error', 'Username atau password salah.');
            return redirect()->back();
        }
    }

    public function register()
    {
        return view('auth/register');
    }

    public function processRegister()
    {
        $role = $this->request->getPost('role');
        
        // Base validation rules
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'full_name' => 'required|min_length[3]|max_length[255]',
            'role' => 'required|in_list[admin,guru,siswa]'
        ];

        // Additional validation for guru
        if ($role === 'guru') {
            $rules['nip'] = 'required|min_length[8]|max_length[20]|is_unique[guru.nip]';
            $rules['bidang_studi'] = 'required|min_length[2]|max_length[100]';
            $rules['jenis_kelamin_guru'] = 'required|in_list[L,P]';
            $rules['no_telp_guru'] = 'required|min_length[10]|max_length[15]';
            $rules['tempat_lahir_guru'] = 'required|min_length[2]|max_length[50]';
            $rules['tanggal_lahir_guru'] = 'required|valid_date';
            $rules['alamat_guru'] = 'required|min_length[10]|max_length[255]';
        }

        // Additional validation for siswa
        if ($role === 'siswa') {
            $rules['nis'] = 'required|min_length[8]|max_length[20]|is_unique[siswa.nis]';
            $rules['jenis_kelamin_siswa'] = 'required|in_list[L,P]';
            $rules['no_telp_siswa'] = 'required|min_length[10]|max_length[15]';
            $rules['kelas_id'] = 'required|numeric';
            $rules['tempat_lahir_siswa'] = 'required|min_length[2]|max_length[50]';
            $rules['tanggal_lahir_siswa'] = 'required|valid_date';
            $rules['alamat_siswa'] = 'required|min_length[10]|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Begin transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user account
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'role' => $role,
                'is_active' => 0 // Default inactive, need admin approval
            ];

            $this->userModel->insert($userData);
            $userId = $this->userModel->getInsertID();

            // Create guru or siswa record based on role
            if ($role === 'guru') {
                $guruData = [
                    'user_id' => $userId,
                    'nip' => $this->request->getPost('nip'),
                    'bidang_studi' => $this->request->getPost('bidang_studi'),
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin_guru'),
                    'no_telp' => $this->request->getPost('no_telp_guru'),
                    'tempat_lahir' => $this->request->getPost('tempat_lahir_guru'),
                    'tanggal_lahir' => $this->request->getPost('tanggal_lahir_guru'),
                    'alamat' => $this->request->getPost('alamat_guru')
                ];
                $this->guruModel->skipValidation(true)->insert($guruData);
            } elseif ($role === 'siswa') {
                $siswaData = [
                    'user_id' => $userId,
                    'nis' => $this->request->getPost('nis'),
                    'jenis_kelamin' => $this->request->getPost('jenis_kelamin_siswa'),
                    'no_telp' => $this->request->getPost('no_telp_siswa'),
                    'kelas_id' => $this->request->getPost('kelas_id'),
                    'tempat_lahir' => $this->request->getPost('tempat_lahir_siswa'),
                    'tanggal_lahir' => $this->request->getPost('tanggal_lahir_siswa'),
                    'alamat' => $this->request->getPost('alamat_siswa')
                ];
                $this->siswaModel->skipValidation(true)->insert($siswaData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            session()->setFlashdata('success', 'Registrasi berhasil! Akun Anda akan diaktivasi oleh admin.');
            return redirect()->to('/auth');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Registration error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }

    private function getRedirectUrl()
    {
        $role = session()->get('role');
        
        switch ($role) {
            case 'admin':
                return '/admin/dashboard';
            case 'guru':
                return '/guru/dashboard';
            case 'siswa':
                return '/siswa/dashboard';
            default:
                return '/auth';
        }
    }
} 