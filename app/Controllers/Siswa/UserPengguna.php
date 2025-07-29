<?php
namespace App\Controllers\Siswa;
use App\Controllers\BaseController;
use App\Models\UserModel;

class UserPengguna extends BaseController {
    public function index() {
        $userModel = new UserModel();
        $userId = session()->get('user_id'); // Mengambil ID user yang sedang login
        $user = $userModel->find($userId);
        
        return view('siswa/user_pengguna/profile', [
            'user' => $user,
            'title' => 'Profile Saya'
        ]);
    }

    public function update() {
        $userId = session()->get('user_id');
        $userModel = new UserModel();
        
        $data = [
            'id' => $userId, // Penting untuk validasi is_unique
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username')
        ];

        // Jika ada password baru
        if ($this->request->getPost('password') != '') {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $data);
        return redirect()->to('siswa/user-pengguna')->with('message', 'Profile berhasil diupdate');
    }
}
