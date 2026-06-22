<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First Steps', 'slug' => 'first-steps', 'description' => 'Earn 10 XP.', 'icon' => '◆', 'criteria_type' => 'xp', 'criteria_value' => 10],
            ['name' => 'Contributor', 'slug' => 'contributor', 'description' => 'Post 10 answers.', 'icon' => '✦', 'criteria_type' => 'answers', 'criteria_value' => 10],
            ['name' => 'Problem Solver', 'slug' => 'problem-solver', 'description' => 'Have 5 answers selected as best.', 'icon' => '✓', 'criteria_type' => 'best_answers', 'criteria_value' => 5],
            ['name' => 'Knowledge Keeper', 'slug' => 'knowledge-keeper', 'description' => 'Earn 1,000 XP.', 'icon' => '★', 'criteria_type' => 'xp', 'criteria_value' => 1000],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
