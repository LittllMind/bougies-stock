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
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('pass'),
                'role' => 'admin',
            ]
        );

        // Employé
        User::updateOrCreate(
            ['email' => 'employe@test.com'],
            [
                'name' => 'Employé Test',
                'password' => Hash::make('pass'),
                'role' => 'employe',
            ]
        );

        // Client
        User::updateOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Client Test',
                'password' => Hash::make('pass'),
                'role' => 'client',
            ]
        );
    }
}
