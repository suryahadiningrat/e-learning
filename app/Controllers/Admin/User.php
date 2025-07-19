<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen User',
            'users' => $this->userModel->findAll(),
        ];

        return view('admin/users', $data);
    }

    public function activate($id)
    {
        if ($this->userModel->activateUser($id)) {
            session()->setFlashdata('success', 'User berhasil diaktivasi.');
        } else {
            session()->setFlashdata('error', 'Gagal mengaktivasi user.');
        }

        return redirect()->to('/admin/users');
    }

    public function deactivate($id)
    {
        if ($this->userModel->deactivateUser($id)) {
            session()->setFlashdata('success', 'User berhasil dinonaktifkan.');
        } else {
            session()->setFlashdata('error', 'Gagal menonaktifkan user.');
        }

        return redirect()->to('/admin/users');
    }
} 