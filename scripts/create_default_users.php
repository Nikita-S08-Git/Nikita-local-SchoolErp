<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

echo "Creating roles...\n";
$roleNames = ['teacher', 'admin', 'principal', 'student', 'accountant', 'office', 'librarian'];
foreach ($roleNames as $roleName) {
    Role::firstOrCreate(['name' => $roleName]);
}
echo "✅ Roles created\n\n";

// Create default users
$users = [
    ['email' => 'admin@schoolerp.com', 'name' => 'System Admin', 'role' => 'admin'],
    ['email' => 'principal@schoolerp.com', 'name' => 'Dr. Principal', 'role' => 'principal'],
    ['email' => 'teacher@schoolerp.com', 'name' => 'John Teacher', 'role' => 'teacher'],
    ['email' => 'student@schoolerp.com', 'name' => 'Test Student', 'role' => 'student'],
    ['email' => 'accountant@schoolerp.com', 'name' => 'Mary Accountant', 'role' => 'accountant'],
    ['email' => 'office@schoolerp.com', 'name' => 'Office Staff', 'role' => 'office'],
    ['email' => 'librarian@schoolerp.com', 'name' => 'Sarah Librarian', 'role' => 'librarian'],
];

echo "Creating users...\n";
foreach ($users as $userData) {
    $u = User::firstOrCreate(
        ['email' => $userData['email']],
        ['name' => $userData['name'], 'password' => Hash::make('password')]
    );
    $u->syncRoles([$userData['role']]);
    echo "✅ {$userData['email']} ({$userData['role']})\n";
}

echo "\n✅ All users created successfully!\n";
echo "All users can login with password: 'password'\n";
