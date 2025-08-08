<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreateTasksSystem extends Migration
{
    public function up()
    {
        // Drop tables if exist
        $this->forge->dropTable('pengumpulan_tugas', true);
        $this->forge->dropTable('tugas', true);

        // Create tugas table
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

        // Create pengumpulan_tugas table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tugas_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'siswa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'link_tugas' => [
                'type' => 'TEXT',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['submitted', 'late', 'reviewed'],
                'default' => 'submitted',
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('tugas_id', 'tugas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['tugas_id', 'siswa_id']); // Siswa hanya bisa submit sekali per tugas
        $this->forge->createTable('pengumpulan_tugas');
    }

    public function down()
    {
        $this->forge->dropTable('pengumpulan_tugas');
        $this->forge->dropTable('tugas');
    }
}
