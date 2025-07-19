<?php
namespace App\Controllers\Guru;
use App\Controllers\BaseController;
use App\Models\UserModel;
class UserPengguna extends BaseController {
    public function index() {
        $userModel = new UserModel();
        $users = $userModel->findAll();
        return view('guru/user_pengguna/index', ['users' => $users, 'title' => 'User Pengguna']);
    }
} 