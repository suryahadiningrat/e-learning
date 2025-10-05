<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingSystem extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Setting System',
            'settings' => $this->getSettings()
        ];

        return view('admin/setting-system/index', $data);
    }

    public function updateLogo()
    {
        // Validasi file
        $rules = [
            'logo' => [
                'uploaded[logo]',
                'mime_in[logo,image/jpg,image/jpeg,image/png,image/gif]',
                'max_size[logo,2048]'
            ]
        ];

        $messages = [
            'logo' => [
                'uploaded' => 'Pilih file logo terlebih dahulu',
                'mime_in' => 'File harus berupa gambar (JPG, JPEG, PNG, GIF)',
                'max_size' => 'Ukuran file maksimal 2MB'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('logo');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Hapus logo lama jika ada
            $oldLogo = $this->getSetting('logo_sekolah');
            if ($oldLogo && file_exists(FCPATH . 'uploads/logo/' . $oldLogo)) {
                unlink(FCPATH . 'uploads/logo/' . $oldLogo);
            }

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/logo')) {
                mkdir(FCPATH . 'uploads/logo', 0777, true);
            }

            // Generate nama file unik
            $newName = 'logo_' . time() . '.' . $file->getExtension();
            
            // Pindahkan file
            if ($file->move(FCPATH . 'uploads/logo', $newName)) {
                // Update setting di database
                $this->updateSetting('logo_sekolah', $newName);
                
                return redirect()->to('admin/setting-system')->with('success', 'Logo sekolah berhasil diperbarui');
            }
        }

        return redirect()->back()->with('error', 'Gagal mengupload logo');
    }

    public function updateBackground()
    {
        // Validasi file
        $rules = [
            'background' => [
                'uploaded[background]',
                'mime_in[background,image/jpg,image/jpeg,image/png,image/gif]',
                'max_size[background,5120]'
            ]
        ];

        $messages = [
            'background' => [
                'uploaded' => 'Pilih file background terlebih dahulu',
                'mime_in' => 'File harus berupa gambar (JPG, JPEG, PNG, GIF)',
                'max_size' => 'Ukuran file maksimal 5MB'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('background');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Hapus background lama jika ada
            $oldBackground = $this->getSetting('background_sistem');
            if ($oldBackground && file_exists(FCPATH . 'uploads/background/' . $oldBackground)) {
                unlink(FCPATH . 'uploads/background/' . $oldBackground);
            }

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/background')) {
                mkdir(FCPATH . 'uploads/background', 0777, true);
            }

            // Generate nama file unik
            $newName = 'background_' . time() . '.' . $file->getExtension();
            
            // Pindahkan file
            if ($file->move(FCPATH . 'uploads/background', $newName)) {
                // Update setting di database
                $this->updateSetting('background_sistem', $newName);
                
                return redirect()->to('admin/setting-system')->with('success', 'Background sistem berhasil diperbarui');
            }
        }

        return redirect()->back()->with('error', 'Gagal mengupload background');
    }

    public function updateTahunAjaran()
    {
        // Validasi input
        $rules = [
            'tahun_ajaran' => 'required|min_length[4]|max_length[9]'
        ];

        $messages = [
            'tahun_ajaran' => [
                'required' => 'Tahun ajaran harus diisi',
                'min_length' => 'Tahun ajaran minimal 4 karakter',
                'max_length' => 'Tahun ajaran maksimal 9 karakter'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tahunAjaran = $this->request->getPost('tahun_ajaran');
        
        // Update setting di database
        $this->updateSetting('tahun_ajaran', $tahunAjaran);
        
        return redirect()->to('admin/setting-system')->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    public function updateLoginBackgroundColor()
    {
        // Validasi input
        $rules = [
            'color' => 'required'
        ];

        $messages = [
            'color' => [
                'required' => 'Warna harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $color = $this->request->getPost('color');
        
        // Update setting di database
        $this->updateSetting('login_background_color', $color);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Warna background login berhasil diperbarui'
        ]);
    }

    public function updateLoginBackgroundImage()
    {
        // Validasi file
        $rules = [
            'background_image' => [
                'uploaded[background_image]',
                'mime_in[background_image,image/jpg,image/jpeg,image/png,image/gif]',
                'max_size[background_image,5120]'
            ]
        ];

        $messages = [
            'background_image' => [
                'uploaded' => 'Pilih file gambar terlebih dahulu',
                'mime_in' => 'File harus berupa gambar (JPG, JPEG, PNG, GIF)',
                'max_size' => 'Ukuran file maksimal 5MB'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('background_image');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Hapus background lama jika ada
            $oldBackground = $this->getSetting('login_background_image');
            if ($oldBackground && file_exists(FCPATH . 'uploads/background/' . $oldBackground)) {
                unlink(FCPATH . 'uploads/background/' . $oldBackground);
            }

            // Buat direktori jika belum ada
            if (!is_dir(FCPATH . 'uploads/background')) {
                mkdir(FCPATH . 'uploads/background', 0777, true);
            }

            // Generate nama file unik
            $newName = 'login_bg_' . time() . '.' . $file->getExtension();
            
            // Pindahkan file
            if ($file->move(FCPATH . 'uploads/background', $newName)) {
                // Update setting di database
                $this->updateSetting('login_background_image', $newName);
                
                return redirect()->to('admin/setting-system')->with('success', 'Gambar background login berhasil diperbarui');
            }
        }

        return redirect()->back()->with('error', 'Gagal mengupload gambar background');
    }

    public function removeLoginBackgroundImage()
    {
        // Hapus gambar background login
        $oldBackground = $this->getSetting('login_background_image');
        if ($oldBackground && file_exists(FCPATH . 'uploads/background/' . $oldBackground)) {
            unlink(FCPATH . 'uploads/background/' . $oldBackground);
        }

        // Hapus setting dari database
        $db = \Config\Database::connect();
        $db->table('settings')->where('key', 'login_background_image')->delete();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Gambar background login berhasil dihapus'
        ]);
    }

    public function updateSidebarColor()
    {
        // Validasi input
        $rules = [
            'role' => 'required|in_list[admin,guru,siswa]',
            'color' => 'required'
        ];

        $messages = [
            'role' => [
                'required' => 'Role harus dipilih',
                'in_list' => 'Role tidak valid'
            ],
            'color' => [
                'required' => 'Warna harus diisi'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $role = $this->request->getPost('role');
        $color = $this->request->getPost('color');
        
        // Update setting di database
        $this->updateSetting('sidebar_color_' . $role, $color);

        // Update session untuk semua warna sidebar
        $db = \Config\Database::connect();
        $colors = $db->table('settings')
            ->whereIn('key', ['sidebar_color_admin', 'sidebar_color_guru', 'sidebar_color_siswa'])
            ->get()
            ->getResultArray();
        
        // Set default colors
        $defaultColors = [
            'admin' => 'linear-gradient(to bottom, #4e73df, #224abe)',
            'guru' => 'linear-gradient(to bottom, #1cc88a, #169b6b)',
            'siswa' => 'linear-gradient(to bottom, #f6c23e, #dda20a)'
        ];

        // Initialize session with default colors first
        foreach ($defaultColors as $roleKey => $defaultColor) {
            session()->set('sidebar_color_' . $roleKey, $defaultColor);
        }

        // Override with colors from database
        foreach ($colors as $colorData) {
            session()->set($colorData['key'], $colorData['value']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Warna sidebar berhasil diperbarui',
            'refresh' => true
        ]);
    }

    private function getSettings()
    {
        $db = \Config\Database::connect();
        $settings = $db->table('settings')->get()->getResultArray();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = $setting['value'];
        }
        
        return $result;
    }

    private function getSetting($key)
    {
        $db = \Config\Database::connect();
        $setting = $db->table('settings')->where('key', $key)->get()->getRowArray();
        
        return $setting ? $setting['value'] : null;
    }

    private function updateSetting($key, $value)
    {
        $db = \Config\Database::connect();
        
        // Cek apakah setting sudah ada
        $existing = $db->table('settings')->where('key', $key)->get()->getRowArray();
        
        if ($existing) {
            // Update existing setting
            $db->table('settings')->where('key', $key)->update([
                'value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert new setting
            $db->table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}