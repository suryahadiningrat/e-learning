<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreateNilaiTable extends Migration
{
    public function up()
    {
        // Drop table if exists
        $this->forge->dropTable('nilai', true);

        // Create new nilai table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'siswa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'jadwal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'nilai_tugas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nilai_ulangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nilai_uts' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'nilai_uas' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jadwal_id', 'jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nilai');
    }

    public function down()
    {
        $this->forge->dropTable('nilai', true);
    }
}
