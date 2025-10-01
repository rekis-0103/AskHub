<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,
            TitleSeeder::class,
            BadWordSeeder::class,
        ]);

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@askhub.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'xp' => 0,
            'level' => 1,
        ]);

        // Create Demo User
        User::create([
            'name' => 'John Doe',
            'email' => 'user@askhub.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'xp' => 0,
            'level' => 1,
        ]);
    }
}
