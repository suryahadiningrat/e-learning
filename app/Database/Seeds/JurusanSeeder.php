<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_jurusan' => 'Teknik Komputer dan Jaringan',
                'kode_jurusan' => 'TKJ',
                'deskripsi' => 'Jurusan yang mempelajari tentang jaringan komputer, sistem operasi, dan administrasi sistem.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
                'kode_jurusan' => 'RPL',
                'deskripsi' => 'Jurusan yang mempelajari tentang pengembangan aplikasi dan software.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_jurusan' => 'Multimedia',
                'kode_jurusan' => 'MM',
                'deskripsi' => 'Jurusan yang mempelajari tentang desain grafis, animasi, dan multimedia.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_jurusan' => 'Teknik Kendaraan Ringan',
                'kode_jurusan' => 'TKR',
                'deskripsi' => 'Jurusan yang mempelajari tentang perbaikan dan perawatan kendaraan ringan.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama_jurusan' => 'Teknik Sepeda Motor',
                'kode_jurusan' => 'TSM',
                'deskripsi' => 'Jurusan yang mempelajari tentang perbaikan dan perawatan sepeda motor.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('jurusan')->insertBatch($data);
        
        echo "Sample jurusan data created successfully!\n";
    }
} 