<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateMateriSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Update existing materi record to use jadwal_id = 2
        // (jadwal_id = 2 is for mata_pelajaran_id = 2)
        $db->table('materi')
           ->where('id', 1)
           ->update(['jadwal_id' => 2]);
        
        echo "Materi data updated successfully\n";
    }
}
