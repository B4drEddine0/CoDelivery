<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the UserSeeder first to create users
        $this->call(UserSeeder::class);
        
        // Then call the CommandSeeder to create commands
        $this->call(CommandSeeder::class);
    }
}
