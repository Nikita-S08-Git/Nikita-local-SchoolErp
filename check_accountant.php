<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking accountant user...\n\n";

$user = App\Models\User::where('email', 'accountant@schoolerp.com')->first();

if ($user) {
    echo "✓ User found: " . $user->name . "\n";
    echo "✓ Email: " . $user->email . "\n";
    echo "✓ User ID: " . $user->id . "\n";
    echo "✓ Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "\n";
    
    // Check if role exists
    $role = $user->roles->first();
    if ($role) {
        echo "✓ First role: " . $role->name . "\n";
    } else {
        echo "✗ NO ROLE ASSIGNED!\n";
    }
} else {
    echo "✗ User not found!\n";
}

echo "\n";
