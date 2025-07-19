<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Admin',
            'total_users' => $this->userModel->countAll(),
            'total_admin' => $this->userModel->where('role', 'admin')->countAllResults(),
            'total_guru' => $this->userModel->where('role', 'guru')->countAllResults(),
            'total_siswa' => $this->userModel->where('role', 'siswa')->countAllResults(),
            'inactive_users' => $this->userModel->where('is_active', 0)->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }
} 