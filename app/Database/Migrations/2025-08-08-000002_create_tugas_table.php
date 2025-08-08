<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTugasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_tugas' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'jadwal_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deadline' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addForeignKey('jadwal_id', 'jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tugas');
    }

    public function down()
    {
        $this->forge->dropTable('tugas');
    }
}
