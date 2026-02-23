<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== EXISTING USER CREDENTIALS ===\n\n";

$users = \App\Models\User::with('roles')->get();

if ($users->isEmpty()) {
    echo "No users found in database.\n";
} else {
    foreach ($users as $user) {
        echo "Email: " . $user->email . "\n";
        echo "Role: " . ($user->roles->first()->name ?? 'No Role') . "\n";
        echo "Name: " . $user->name . "\n";
        echo "Password: (Check seeder files for original passwords)\n";
        echo "---\n\n";
    }
}

echo "\nNote: Passwords are hashed in database. Check seeder files for original passwords.\n";
echo "Common test passwords: admin123, password123, password\n";
