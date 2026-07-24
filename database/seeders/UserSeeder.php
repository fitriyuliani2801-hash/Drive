<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Kota Metro
        User::updateOrCreate(
            ['email' => 'admin@metrologi.go.id'],
            [
                'name' => 'Admin Pemkot Metro',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081277889900',
            ]
        );

        // Sample Citizen users
        User::updateOrCreate(
            ['email' => 'budi@warga.metro.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'phone' => '085211223344',
            ]
        );

        User::updateOrCreate(
            ['email' => 'siti@warga.metro.id'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'phone' => '081399887766',
            ]
        );
    }
}
