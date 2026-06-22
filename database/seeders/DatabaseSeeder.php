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
            BadgeSeeder::class,
        ]);

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@askhub.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'xp' => 0,
            'level' => 1,
        ]);

        // Create Demo User
        User::create([
            'name' => 'John Doe',
            'username' => 'john_doe',
            'email' => 'user@askhub.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'xp' => 0,
            'level' => 1,
        ]);
    }
}
