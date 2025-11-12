<?php
namespace App\Controllers\Siswa;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SiswaModel;

class UserPengguna extends BaseController {
    public function index() {
        $userModel = new UserModel();
        $siswaModel = new SiswaModel();
        $userId = session()->get('user_id'); // Mengambil ID user yang sedang login
        $user = $userModel->find($userId);
        
        // Get siswa details with kelas info
        $db = \Config\Database::connect();
        $siswa = $db->table('siswa s')
            ->select('s.*, CONCAT(k.tingkat, " ", k.kode_jurusan, " ", k.paralel) as nama_kelas, jur.nama_jurusan')
            ->join('kelas k', 'k.id = s.kelas_id')
            ->join('jurusan jur', 'jur.id = k.jurusan_id')
            ->where('s.user_id', $userId)
            ->get()
            ->getRowArray();
        
        return view('siswa/user_pengguna/profile', [
            'user' => $user,
            'siswa' => $siswa,
            'title' => 'Profile Saya'
        ]);
    }

    public function update() {
        $userId = session()->get('user_id');
        $userModel = new UserModel();

        // Validasi input
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email,id,'.$userId.']',
            'username' => 'required|min_length[3]|is_unique[users.username,id,'.$userId.']',
            'photo' => 'permit_empty|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]|max_size[photo,2048]'
        ];

        // Tambah validasi password jika diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'required|min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle photo upload
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/profile', $newName);
            
            // Delete old photo if exists
            $oldUser = $userModel->find($userId);
            if ($oldUser['photo'] && file_exists(FCPATH . 'uploads/profile/' . $oldUser['photo'])) {
                unlink(FCPATH . 'uploads/profile/' . $oldUser['photo']);
            }
        }

        // Siapkan data untuk update
        $data = [
            'id' => $userId, // Penting untuk validasi is_unique
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username')
        ];

        // Add photo to data if uploaded
        if (isset($newName)) {
            $data['photo'] = $newName;
        }

        // Jika ada password baru
        if ($this->request->getPost('password') != '') {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $data);
        return redirect()->to('siswa/user-pengguna')->with('message', 'Profile berhasil diupdate');
    }
}
