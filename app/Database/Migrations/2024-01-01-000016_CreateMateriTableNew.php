<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMateriTableNew extends Migration
{
    public function up()
    {
        // Drop existing table if exists
        $this->db->query('DROP TABLE IF EXISTS materi');
        
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'mata_pelajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_size' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('materi');
    }

    public function down()
    {
        $this->forge->dropTable('materi');
    }
} 