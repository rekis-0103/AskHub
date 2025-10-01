<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['level' => 1, 'xp_required' => 0, 'name' => 'Newbie'],
            ['level' => 2, 'xp_required' => 100, 'name' => 'Beginner'],
            ['level' => 3, 'xp_required' => 250, 'name' => 'Learner'],
            ['level' => 4, 'xp_required' => 500, 'name' => 'Contributor'],
            ['level' => 5, 'xp_required' => 1000, 'name' => 'Expert'],
            ['level' => 6, 'xp_required' => 2000, 'name' => 'Master'],
            ['level' => 7, 'xp_required' => 4000, 'name' => 'Guru'],
            ['level' => 8, 'xp_required' => 8000, 'name' => 'Legend'],
            ['level' => 9, 'xp_required' => 15000, 'name' => 'Mythical'],
            ['level' => 10, 'xp_required' => 30000, 'name' => 'God'],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
