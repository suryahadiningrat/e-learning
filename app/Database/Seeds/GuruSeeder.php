<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        // First create users for teachers
        $users = [
            [
                'username' => 'guru1',
                'email' => 'guru1@smk.edu',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Dr. Ahmad Wijaya, S.Kom',
                'role' => 'guru',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'guru2',
                'email' => 'guru2@smk.edu',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Prof. Siti Nurhaliza, S.Pd',
                'role' => 'guru',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'guru3',
                'email' => 'guru3@smk.edu',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Budi Santoso, S.T',
                'role' => 'guru',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('users')->insertBatch($users);
        
        // Get the inserted user IDs
        $userIds = [];
        $usersResult = $this->db->table('users')
                               ->select('id, username')
                               ->whereIn('username', ['guru1', 'guru2', 'guru3'])
                               ->get()
                               ->getResultArray();
        
        foreach ($usersResult as $user) {
            $userIds[$user['username']] = $user['id'];
        }

        // Now create teacher data with correct user_ids
        $guruData = [
            [
                'user_id' => $userIds['guru1'],
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
                'user_id' => $userIds['guru2'],
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
                'user_id' => $userIds['guru3'],
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

        $this->db->table('guru')->insertBatch($guruData);
        
        echo "Sample guru users and data created successfully!\n";
        echo "Teacher logins:\n";
        echo "- guru1 / guru123\n";
        echo "- guru2 / guru123\n";
        echo "- guru3 / guru123\n";
    }
} 