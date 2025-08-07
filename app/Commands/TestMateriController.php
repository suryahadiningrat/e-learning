<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\Guru\Materi as GuruMateri;
use App\Controllers\Admin\Materi as AdminMateri;

class TestMateriController extends BaseCommand
{
    protected $group = 'Test';
    protected $name = 'test:materi-controller';
    protected $description = 'Test MateriController to ensure jadwal data is passed correctly.';

    public function run(array $params)
    {
        CLI::write('Testing MateriController...', 'yellow');
        
        // Test Guru Materi Controller
        CLI::write('Testing Guru/Materi Controller:');
        
        // Simulate session
        session()->set('user_id', 1);
        
        $guruController = new GuruMateri();
        
        try {
            // Test create method
            $result = $guruController->create();
            CLI::write('Guru create method - Success!', 'green');
        } catch (\Exception $e) {
            CLI::write('Guru create method - Error: ' . $e->getMessage(), 'red');
        }
        
        // Test Admin Materi Controller  
        CLI::write('Testing Admin/Materi Controller:');
        
        $adminController = new AdminMateri();
        
        try {
            // Test create method
            $result = $adminController->create();
            CLI::write('Admin create method - Success!', 'green');
        } catch (\Exception $e) {
            CLI::write('Admin create method - Error: ' . $e->getMessage(), 'red');
        }
        
        CLI::write('Test completed!', 'green');
    }
}
