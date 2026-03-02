<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "═══════════════════════════════════════════════" . PHP_EOL;
echo "     RUNNING MANUAL MIGRATIONS & SEEDING      " . PHP_EOL;
echo "═══════════════════════════════════════════════" . PHP_EOL . PHP_EOL;

try {
    // Check connection
    DB::connection()->getPdo();
    echo "✅ Database Connected" . PHP_EOL . PHP_EOL;
    
    // Read SQL file
    $sqlFile = __DIR__ . '/database/manual_migrations.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   !preg_match('/^--/', $stmt) && 
                   !preg_match('/^\/\*/', $stmt);
        }
    );
    
    echo "Found " . count($statements) . " SQL statements" . PHP_EOL . PHP_EOL;
    
    // Execute each statement
    $count = 0;
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            DB::statement($statement);
            $count++;
            
            // Show progress for CREATE/INSERT statements
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`(\w+)`/i', $statement, $matches);
                if (isset($matches[1])) {
                    echo "   ✓ Created table: {$matches[1]}" . PHP_EOL;
                }
            }
        } catch (\Exception $e) {
            // Skip errors for "already exists" and "column exists" errors
            if (strpos($e->getMessage(), 'already exists') === false &&
                strpos($e->getMessage(), 'Duplicate column') === false &&
                strpos($e->getMessage(), 'doesn\'t exist') === false) {
                echo "   ⚠️  Warning: " . $e->getMessage() . PHP_EOL;
            }
        }
    }
    
    echo PHP_EOL . "✅ Executed {$count} SQL statements" . PHP_EOL . PHP_EOL;
    
    // Now run the seeder
    echo "═══════════════════════════════════════════════" . PHP_EOL;
    echo "            RUNNING SEEDER                     " . PHP_EOL;
    echo "═══════════════════════════════════════════════" . PHP_EOL . PHP_EOL;
    
    Artisan::call('db:seed', ['--class' => 'GlobalTimetableSeeder']);
    echo Artisan::output();
    
    echo PHP_EOL . "═══════════════════════════════════════════════" . PHP_EOL;
    echo "          SETUP COMPLETE! 🎉             " . PHP_EOL;
    echo "═══════════════════════════════════════════════" . PHP_EOL;
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
