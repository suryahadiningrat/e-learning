<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemesterColumnsToNilai extends Migration
{
    public function up()
    {
        // Remove old single UTS/UAS columns
        $this->forge->dropColumn('nilai', ['nilai_uts', 'nilai_uas']);
        
        // Add semester-specific columns
        $fields = [
            'nilai_uts_sem1' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'nilai_ulangan'
            ],
            'nilai_uas_sem1' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'nilai_uts_sem1'
            ],
            'nilai_uts_sem2' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'nilai_uas_sem1'
            ],
            'nilai_uas_sem2' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'nilai_uts_sem2'
            ],
        ];
        
        $this->forge->addColumn('nilai', $fields);
    }

    public function down()
    {
        // Remove semester-specific columns
        $this->forge->dropColumn('nilai', ['nilai_uts_sem1', 'nilai_uas_sem1', 'nilai_uts_sem2', 'nilai_uas_sem2']);
        
        // Add back old columns
        $fields = [
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
            ],
        ];
        
        $this->forge->addColumn('nilai', $fields);
    }
}
