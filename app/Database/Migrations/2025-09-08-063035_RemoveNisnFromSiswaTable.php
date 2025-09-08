<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveNisnFromSiswaTable extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('siswa', 'nisn');
    }

    public function down()
    {
        $this->forge->addColumn('siswa', [
            'nisn' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
                'after' => 'id'
            ]
        ]);
    }
}
