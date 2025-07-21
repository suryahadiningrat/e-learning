<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMateriTable extends Migration
{
    public function up()
    {
        // Check if foreign key exists before dropping
        $foreignKeys = $this->db->getForeignKeyData('materi');
        foreach ($foreignKeys as $fk) {
            if ($fk->constraint_name === 'materi_mata_pelajaran_foreign') {
                $this->forge->dropForeignKey('materi', 'materi_mata_pelajaran_foreign');
                break;
            }
        }
        
        // Check if column exists before dropping
        $fields = $this->db->getFieldNames('materi');
        if (in_array('mata_pelajaran', $fields)) {
            // Drop column mata_pelajaran
            $this->forge->dropColumn('materi', 'mata_pelajaran');
        }
        
        // Check if column mata_pelajaran_id doesn't exist
        if (!in_array('mata_pelajaran_id', $fields)) {
            // Add column mata_pelajaran_id
            $this->forge->addColumn('materi', [
                'mata_pelajaran_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'after' => 'judul'
                ]
            ]);
        }
        
        // Add foreign key if not exists
        $foreignKeys = $this->db->getForeignKeyData('materi');
        $fkExists = false;
        foreach ($foreignKeys as $fk) {
            if ($fk->constraint_name === 'materi_mata_pelajaran_id_foreign') {
                $fkExists = true;
                break;
            }
        }
        
        if (!$fkExists) {
            $this->forge->addForeignKey('mata_pelajaran_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE', 'materi_mata_pelajaran_id_foreign');
        }
    }

    public function down()
    {
        // Drop foreign key if exists
        $foreignKeys = $this->db->getForeignKeyData('materi');
        foreach ($foreignKeys as $fk) {
            if ($fk->constraint_name === 'mata_pelajaran_id_foreign') {
                $this->forge->dropForeignKey('materi', 'materi_mata_pelajaran_id_foreign');
                break;
            }
        }
        
        // Drop column mata_pelajaran_id if exists
        $fields = $this->db->getFieldNames('materi');
        if (in_array('mata_pelajaran_id', $fields)) {
            $this->forge->dropColumn('materi', 'mata_pelajaran_id');
        }
        
        // Add back column mata_pelajaran if not exists
        if (!in_array('mata_pelajaran', $fields)) {
            $this->forge->addColumn('materi', [
                'mata_pelajaran' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => false,
                    'after' => 'judul'
                ]
            ]);
        }
    }
} 