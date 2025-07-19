<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        // Create teacher data (assuming user_id 1, 2, 3 exist)
        $data = [
            [
                'user_id' => 1,
                'nip' => '19850101001',
                'bidang_studi' => 'Pemrograman Web',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-01-01',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                'no_telp' => '081234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 2,
                'nip' => '19860202002',
                'bidang_studi' => 'Matematika',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1986-02-02',
                'alamat' => 'Jl. Asia Afrika No. 456, Bandung',
                'no_telp' => '081234567891',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => 3,
                'nip' => '19870303003',
                'bidang_studi' => 'Jaringan Komputer',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1987-03-03',
                'alamat' => 'Jl. Tunjungan No. 789, Surabaya',
                'no_telp' => '081234567892',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('guru')->insertBatch($data);
        
        echo "Sample guru data created successfully!\n";
    }
} 