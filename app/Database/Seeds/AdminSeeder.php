<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();
        
        // Check if admin already exists
        $existingAdmin = $userModel->where('username', 'admin')->first();
        
        if (!$existingAdmin) {
            $userModel->insert([
                'username' => 'admin',
                'email' => 'admin@smk.edu',
                'password' => '$2a$12$DEYkzf/j.Ymq7q2DDE7aZulVfMzbt47p01GjJR1VCNvX46pY6EIwa',
                'full_name' => 'Administrator',
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "Admin user created successfully!\n";
            echo "Username: admin\n";
            echo "Password: admin123\n";
        } else {
            echo "Admin user already exists!\n";
        }
    }
} 