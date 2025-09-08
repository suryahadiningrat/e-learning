<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNilaiFieldsToNilaiTable extends Migration
{
    public function up()
    {
        $fields = [
            'nilai_tugas' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'jadwal_id'
            ],
            'nilai_ulangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nilai_tugas'
            ],
            'nilai_uts' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'nilai_ulangan'
            ],
            'nilai_uas' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'nilai_uts'
            ]
        ];

        $this->forge->addColumn('nilai', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('nilai', ['nilai_tugas', 'nilai_ulangan', 'nilai_uts', 'nilai_uas']);
    }
}
