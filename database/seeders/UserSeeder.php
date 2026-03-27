<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('pass'),
            'role' => 'admin',
        ]);

        // Employé
        User::create([
            'name' => 'Employé Test',
            'email' => 'employe@test.com',
            'password' => Hash::make('pass'),
            'role' => 'employe',
        ]);

        // Client
        User::create([
            'name' => 'Client Test',
            'email' => 'client@test.com',
            'password' => Hash::make('pass'),
            'role' => 'client',
        ]);
    }
}
