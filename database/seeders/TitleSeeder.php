<?php

namespace Database\Seeders;

use App\Models\Title;
use Illuminate\Database\Seeder;

class TitleSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            [
                'name' => '🌱 Seedling',
                'description' => 'Just starting out',
                'required_level' => 1,
                'color' => '#22c55e'
            ],
            [
                'name' => '📚 Scholar',
                'description' => 'Eager to learn',
                'required_level' => 2,
                'color' => '#3b82f6'
            ],
            [
                'name' => '💡 Innovator',
                'description' => 'Brings fresh ideas',
                'required_level' => 3,
                'color' => '#f59e0b'
            ],
            [
                'name' => '⭐ Rising Star',
                'description' => 'Making an impact',
                'required_level' => 4,
                'color' => '#eab308'
            ],
            [
                'name' => '🎯 Sharpshooter',
                'description' => 'Accurate and helpful',
                'required_level' => 5,
                'color' => '#ef4444'
            ],
            [
                'name' => '👑 Elite',
                'description' => 'Among the best',
                'required_level' => 6,
                'color' => '#8b5cf6'
            ],
            [
                'name' => '🔥 Unstoppable',
                'description' => 'Consistently excellent',
                'required_level' => 7,
                'color' => '#f97316'
            ],
            [
                'name' => '⚡ Lightning',
                'description' => 'Quick and brilliant',
                'required_level' => 8,
                'color' => '#06b6d4'
            ],
            [
                'name' => '🌟 Legendary',
                'description' => 'Stories are told',
                'required_level' => 9,
                'color' => '#d946ef'
            ],
            [
                'name' => '👼 Divine',
                'description' => 'Transcended mortal limits',
                'required_level' => 10,
                'color' => '#fbbf24'
            ],
        ];

        foreach ($titles as $title) {
            Title::create($title);
        }
    }
}
