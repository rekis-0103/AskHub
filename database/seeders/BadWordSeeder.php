<?php

namespace Database\Seeders;

use App\Models\BadWord;
use Illuminate\Database\Seeder;

class BadWordSeeder extends Seeder
{
    public function run(): void
    {
        $badWords = [
            'anjing',
            'babi',
            'bangsat',
            'brengsek',
            'bodoh',
            'goblok',
            'idiot',
            'tolol',
            'kampret',
            'kontol',
            'memek',
            'ngentot',
            'bajingan',
            'asu',
            'jancok',
            'cuk',
            'fuck',
            'shit',
            'bitch',
            'damn',
        ];

        foreach ($badWords as $word) {
            BadWord::create(['word' => $word]);
        }
    }
}
