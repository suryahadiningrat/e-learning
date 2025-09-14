<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'tingkat' => 'X',
                'kode_jurusan' => 'TKJ',
                'paralel' => '1',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'X',
                'kode_jurusan' => 'TKJ',
                'paralel' => '2',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'X',
                'kode_jurusan' => 'RPL',
                'paralel' => '1',
                'jurusan_id' => 2, // RPL
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'X',
                'kode_jurusan' => 'MM',
                'paralel' => '1',
                'jurusan_id' => 3, // MM
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'XI',
                'kode_jurusan' => 'TKJ',
                'paralel' => '1',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'XI',
                'kode_jurusan' => 'RPL',
                'paralel' => '1',
                'jurusan_id' => 2, // RPL
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'XII',
                'kode_jurusan' => 'TKJ',
                'paralel' => '1',
                'jurusan_id' => 1, // TKJ
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'tingkat' => 'XII',
                'kode_jurusan' => 'RPL',
                'paralel' => '1',
                'jurusan_id' => 2, // RPL
                'kapasitas' => 36,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('kelas')->insertBatch($data);
        
        echo "Sample kelas data created successfully!\n";
    }
} 