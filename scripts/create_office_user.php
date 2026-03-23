<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Create office role if missing
Role::firstOrCreate(['name' => 'office']);

// Create office user
$office = User::firstOrCreate(
    ['email' => 'office@schoolerp.com'],
    ['name' => 'Office Staff', 'password' => Hash::make('password')]
);
$office->syncRoles(['office']);

echo "✅ Office user created: office@schoolerp.com\n";
echo "✅ Login with password: 'password'\n";
