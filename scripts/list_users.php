<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// bootstrap the application so Eloquent works
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Create roles if they don't exist
$roleNames = ['teacher', 'admin', 'principal', 'student', 'accountant', 'office', 'librarian'];
foreach ($roleNames as $roleName) {
    Role::firstOrCreate(['name' => $roleName]);
}
echo "✅ Roles created/verified\n\n";

// Reset all passwords to 'password' and assign roles
$users = [
    'teacher@schoolerp.com' => 'teacher',
    'admin@schoolerp.com' => 'admin',
    'principal@schoolerp.com' => 'principal',
    'student@schoolerp.com' => 'student',
    'accountant@schoolerp.com' => 'accountant',
    'office@schoolerp.com' => 'office',
    'librarian@schoolerp.com' => 'librarian',
];

foreach ($users as $email => $role) {
    $u = User::where('email', $email)->first();
    if ($u) {
        $u->password = Hash::make('password');
        $u->save();
        $u->syncRoles([$role]);
        echo "✅ Updated $email - password reset, role: $role\n";
    } else {
        echo "⚠️  User not found: $email\n";
    }
}

echo "\n✅ All authentication credentials fixed!\n";
echo "You can now login with password: 'password'\n";
