<?php
require_once FCPATH . '../vendor/autoload.php';

// Simple test to verify the Guru/Materi controller can be loaded
try {
    echo "Testing Guru/Materi Controller instantiation...\n";
    
    // Check if the class exists first
    if (class_exists('App\\Controllers\\Guru\\Materi')) {
        echo "✓ Class exists: App\\Controllers\\Guru\\Materi\n";
        
        // Try to create an instance
        $controller = new \App\Controllers\Guru\Materi();
        echo "✓ Controller instantiated successfully\n";
        
        echo "✓ All tests passed!\n";
    } else {
        echo "✗ Class not found: App\\Controllers\\Guru\\Materi\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
