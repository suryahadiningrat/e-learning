<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemesterAndTahunAjaranToJadwalTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('jadwal', [
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['Ganjil', 'Genap'],
                'after'      => 'jam_selesai',
            ],
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 9,
                'after'      => 'semester',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('jadwal', ['semester', 'tahun_ajaran']);
    }
} 