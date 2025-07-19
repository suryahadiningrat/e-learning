<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_kelas' => 'X TKJ 1',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'tingkat' => 1, // X
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'X TKJ 2',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'tingkat' => 1, // X
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'X RPL 1',
                'jurusan_id' => 2, // RPL
                'kapasitas' => 36,
                'tingkat' => 1, // X
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'X MM 1',
                'jurusan_id' => 3, // MM
                'kapasitas' => 36,
                'tingkat' => 1, // X
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'XI TKJ 1',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'tingkat' => 2, // XI
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'XI RPL 1',
                'jurusan_id' => 2, // RPL
                'kapasitas' => 36,
                'tingkat' => 2, // XI
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'XII TKJ 1',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'tingkat' => 3, // XII
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_kelas' => 'XII RPL 1',
                'jurusan_id' => 2, // RPL
                'kapasitas' => 36,
                'tingkat' => 3, // XII
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('kelas')->insertBatch($data);
        
        echo "Sample kelas data created successfully!\n";
    }
} 