<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAbsensiTableForHariAbsensi extends Migration
{
    public function up()
    {
        // Add hari_absensi_id column
        $this->forge->addColumn('absensi', [
            'hari_absensi_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'siswa_id'
            ]
        ]);

        // Add foreign key for hari_absensi_id
        $this->forge->addForeignKey('hari_absensi_id', 'hari_absensi', 'id', 'CASCADE', 'CASCADE', 'absensi');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('absensi', 'absensi_hari_absensi_id_foreign');
        
        // Drop the column
        $this->forge->dropColumn('absensi', 'hari_absensi_id');
    }
}
