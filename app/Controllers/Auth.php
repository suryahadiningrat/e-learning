<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
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
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'full_name' => 'required|min_length[3]|max_length[255]',
            'role' => 'required|in_list[admin,guru,siswa]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $this->request->getPost('role'),
            'is_active' => 0 // Default inactive, need admin approval
        ];

        if ($this->userModel->insert($data)) {
            session()->setFlashdata('success', 'Registrasi berhasil! Akun Anda akan diaktivasi oleh admin.');
            return redirect()->to('/auth');
        } else {
            session()->setFlashdata('error', 'Terjadi kesalahan saat registrasi.');
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