<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyMateriTable extends Migration
{
    public function up()
    {
        // Drop mata_pelajaran_id column (this will also drop any foreign key)
        $this->forge->dropColumn('materi', 'mata_pelajaran_id');
        
        // Add jadwal_id column
        $fields = [
            'jadwal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'after' => 'judul'
            ]
        ];
        
        $this->forge->addColumn('materi', $fields);
    }

    public function down()
    {
        // Drop jadwal_id column
        $this->forge->dropColumn('materi', 'jadwal_id');
        
        // Add back mata_pelajaran_id column
        $fields = [
            'mata_pelajaran_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'after' => 'judul'
            ]
        ];
        
        $this->forge->addColumn('materi', $fields);
    }
}
