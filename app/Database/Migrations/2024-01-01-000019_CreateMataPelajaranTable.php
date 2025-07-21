<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMataPelajaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'unique'     => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
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
        $this->forge->createTable('mata_pelajaran');
        
        // Insert data default mata pelajaran
        $data = [
            ['kode' => 'MTK', 'nama' => 'Matematika', 'deskripsi' => 'Mata pelajaran matematika'],
            ['kode' => 'BIN', 'nama' => 'Bahasa Indonesia', 'deskripsi' => 'Mata pelajaran bahasa Indonesia'],
            ['kode' => 'BIG', 'nama' => 'Bahasa Inggris', 'deskripsi' => 'Mata pelajaran bahasa Inggris'],
            ['kode' => 'IPA', 'nama' => 'Ilmu Pengetahuan Alam', 'deskripsi' => 'Mata pelajaran IPA'],
            ['kode' => 'IPS', 'nama' => 'Ilmu Pengetahuan Sosial', 'deskripsi' => 'Mata pelajaran IPS'],
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam', 'deskripsi' => 'Mata pelajaran pendidikan agama'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan', 'deskripsi' => 'Mata pelajaran PKN'],
            ['kode' => 'SBD', 'nama' => 'Seni Budaya', 'deskripsi' => 'Mata pelajaran seni budaya'],
            ['kode' => 'PJK', 'nama' => 'Pendidikan Jasmani', 'deskripsi' => 'Mata pelajaran olahraga'],
            ['kode' => 'TIK', 'nama' => 'Teknologi Informasi', 'deskripsi' => 'Mata pelajaran komputer'],
            ['kode' => 'EKO', 'nama' => 'Ekonomi', 'deskripsi' => 'Mata pelajaran ekonomi'],
            ['kode' => 'SEJ', 'nama' => 'Sejarah', 'deskripsi' => 'Mata pelajaran sejarah'],
            ['kode' => 'GEO', 'nama' => 'Geografi', 'deskripsi' => 'Mata pelajaran geografi'],
            ['kode' => 'FIS', 'nama' => 'Fisika', 'deskripsi' => 'Mata pelajaran fisika'],
            ['kode' => 'KIM', 'nama' => 'Kimia', 'deskripsi' => 'Mata pelajaran kimia'],
            ['kode' => 'BIO', 'nama' => 'Biologi', 'deskripsi' => 'Mata pelajaran biologi'],
        ];
        
        $this->db->table('mata_pelajaran')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('mata_pelajaran');
    }
} 