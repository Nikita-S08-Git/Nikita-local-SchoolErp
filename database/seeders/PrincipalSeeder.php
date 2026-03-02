<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class PrincipalSeeder extends Seeder
{
    public function run()
    {
        // Create principal role if it doesn't exist
        $principalRole = Role::firstOrCreate(['name' => 'principal']);
        
        // Create test principal
        $principal = User::create([
            'name' => 'Dr. Principal',
            'email' => 'principal@school.com',
            'password' => Hash::make('admin123'),
        ]);
        
        $principal->assignRole('principal');
        
        echo "Principal created:\n";
        echo "Email: principal@school.com\n";
        echo "Password: admin123\n";
    }
}