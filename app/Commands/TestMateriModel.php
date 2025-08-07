<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\MateriModel;

class TestMateriModel extends BaseCommand
{
    protected $group = 'Test';
    protected $name = 'test:materi';
    protected $description = 'Test MateriModel with new jadwal_id field.';

    public function run(array $params)
    {
        $materiModel = new MateriModel();
        
        CLI::write('Testing MateriModel...', 'yellow');
        
        // Test getAllMateri
        CLI::write('Testing getAllMateri():');
        $materi = $materiModel->getAllMateri();
        CLI::write('Count: ' . count($materi));
        if (!empty($materi)) {
            CLI::write('First record: ' . json_encode($materi[0]));
        }
        
        // Test getMateriWithRelations
        CLI::write('Testing getMateriWithRelations():');
        $materiWithRelations = $materiModel->getMateriWithRelations();
        CLI::write('Count: ' . count($materiWithRelations));
        if (!empty($materiWithRelations)) {
            CLI::write('First record: ' . json_encode($materiWithRelations[0]));
        }
        
        CLI::write('Test completed!', 'green');
    }
}
