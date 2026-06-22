<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    public function sync(Question $question, array $names): void
    {
        $ids = collect($names)
            ->map(fn (string $name) => trim(Str::lower($name)))
            ->filter()
            ->unique()
            ->take(5)
            ->map(function (string $name) {
                $slug = Str::slug($name);

                return Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $name]
                )->id;
            });

        $question->tags()->sync($ids);
    }
}
