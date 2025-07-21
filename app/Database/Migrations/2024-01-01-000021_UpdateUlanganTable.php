<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUlanganTable extends Migration
{
    public function up()
    {
        // Tambah kolom mata_pelajaran_id
        $this->forge->addColumn('ulangan', [
            'mata_pelajaran_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kelas_id'
            ]
        ]);
        
        // Tambah foreign key
        $this->forge->addForeignKey('mata_pelajaran_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        
        // Update data ulangan yang ada dengan mapping mata pelajaran
        $mapping = [
            'Matematika' => 1,
            'Bahasa Indonesia' => 2,
            'Bahasa Inggris' => 3,
            'IPA' => 4,
            'IPS' => 5,
            'Pendidikan Agama' => 6,
            'Pendidikan Kewarganegaraan' => 7,
            'Seni Budaya' => 8,
            'Pendidikan Jasmani' => 9,
            'Teknologi Informasi' => 10,
            'Ekonomi' => 11,
            'Sejarah' => 12,
            'Geografi' => 13,
            'Fisika' => 14,
            'Kimia' => 15,
            'Biologi' => 16,
        ];
        
        foreach ($mapping as $nama => $id) {
            $this->db->query("UPDATE ulangan SET mata_pelajaran_id = ? WHERE mata_pelajaran = ?", [$id, $nama]);
        }
        
        // Hapus kolom mata_pelajaran lama
        $this->forge->dropColumn('ulangan', 'mata_pelajaran');
    }

    public function down()
    {
        // Tambah kembali kolom mata_pelajaran
        $this->forge->addColumn('ulangan', [
            'mata_pelajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'after'      => 'kelas_id'
            ]
        ]);
        
        // Update data kembali ke string
        $mapping = [
            1 => 'Matematika',
            2 => 'Bahasa Indonesia',
            3 => 'Bahasa Inggris',
            4 => 'IPA',
            5 => 'IPS',
            6 => 'Pendidikan Agama',
            7 => 'Pendidikan Kewarganegaraan',
            8 => 'Seni Budaya',
            9 => 'Pendidikan Jasmani',
            10 => 'Teknologi Informasi',
            11 => 'Ekonomi',
            12 => 'Sejarah',
            13 => 'Geografi',
            14 => 'Fisika',
            15 => 'Kimia',
            16 => 'Biologi',
        ];
        
        foreach ($mapping as $id => $nama) {
            $this->db->query("UPDATE ulangan SET mata_pelajaran = ? WHERE mata_pelajaran_id = ?", [$nama, $id]);
        }
        
        // Hapus foreign key dan kolom mata_pelajaran_id
        $this->forge->dropForeignKey('ulangan', 'ulangan_mata_pelajaran_id_foreign');
        $this->forge->dropColumn('ulangan', 'mata_pelajaran_id');
    }
} 