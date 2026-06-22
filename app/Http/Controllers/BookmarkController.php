<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Question;

class BookmarkController extends Controller
{
    public function store(Question $question)
    {
        Bookmark::firstOrCreate([
            'user_id' => auth()->id(),
            'question_id' => $question->id,
        ]);

        return back()->with('success', 'Question saved.');
    }

    public function destroy(Question $question)
    {
        Bookmark::query()->where('user_id', auth()->id())
            ->where('question_id', $question->id)->delete();

        return back()->with('success', 'Question removed from saved items.');
    }
}
