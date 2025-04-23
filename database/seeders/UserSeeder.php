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
        // Create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@codelivery.com',
            'phone' => '0600000000',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        // Create client users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'first_name' => "Client{$i}",
                'last_name' => 'User',
                'email' => "client{$i}@example.com",
                'phone' => "060000000{$i}",
                'password' => Hash::make('password'),
                'role' => 'client',
            ]);
        }
        
        // Create livreur users
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'first_name' => "Livreur{$i}",
                'last_name' => 'User',
                'email' => "livreur{$i}@example.com",
                'phone' => "070000000{$i}",
                'password' => Hash::make('password'),
                'role' => 'livreur',
            ]);
        }
    }
}
