<?php
/**
 * This script adds the missing document columns to the admissions table.
 * Run this file in your browser or via command line.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding document columns to admissions table...\n";

try {
    // Check if columns already exist
    $columns = DB::select('SHOW COLUMNS FROM admissions');
    $columnNames = array_column($columns, 'Field');
    
    if (!in_array('photo_path', $columnNames)) {
        DB::statement('ALTER TABLE admissions ADD COLUMN photo_path VARCHAR(255) NULL');
        echo "Added photo_path column\n";
    } else {
        echo "photo_path column already exists\n";
    }
    
    if (!in_array('signature_path', $columnNames)) {
        DB::statement('ALTER TABLE admissions ADD COLUMN signature_path VARCHAR(255) NULL');
        echo "Added signature_path column\n";
    } else {
        echo "signature_path column already exists\n";
    }
    
    if (!in_array('twelfth_marksheet_path', $columnNames)) {
        DB::statement('ALTER TABLE admissions ADD COLUMN twelfth_marksheet_path VARCHAR(255) NULL');
        echo "Added twelfth_marksheet_path column\n";
    } else {
        echo "twelfth_marksheet_path column already exists\n";
    }
    
    if (!in_array('cast_certificate_path', $columnNames)) {
        DB::statement('ALTER TABLE admissions ADD COLUMN cast_certificate_path VARCHAR(255) NULL');
        echo "Added cast_certificate_path column\n";
    } else {
        echo "cast_certificate_path column already exists\n";
    }
    
    echo "\nDone! All columns have been added successfully.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
