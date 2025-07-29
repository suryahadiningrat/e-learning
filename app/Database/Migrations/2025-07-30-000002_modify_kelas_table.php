<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyKelasTable extends Migration
{
    public function up()
    {
        // Drop existing columns
        $this->forge->dropColumn('kelas', ['nama_kelas', 'tingkat']);

        // Add new columns
        $this->forge->addColumn('kelas', [
            'tingkat' => [
                'type' => 'ENUM',
                'constraint' => ['X', 'XI', 'XII'],
                'null' => false,
                'after' => 'id'
            ],
            'kode_jurusan' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'after' => 'tingkat'
            ],
            'paralel' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null' => false,
                'after' => 'kode_jurusan'
            ]
        ]);

        // Add unique constraint
        $this->forge->addKey(['tingkat', 'kode_jurusan', 'paralel'], true);
    }

    public function down()
    {
        // Remove new columns
        $this->forge->dropColumn('kelas', ['tingkat', 'kode_jurusan', 'paralel']);

        // Add back original columns
        $this->forge->addColumn('kelas', [
            'nama_kelas' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'tingkat' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => false
            ]
        ]);
    }
}
