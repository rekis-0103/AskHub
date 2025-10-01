<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadWord;
use Illuminate\Http\Request;

class BadWordController extends Controller
{
    public function index()
    {
        $badWords = BadWord::latest()->paginate(50);
        return view('admin.badwords.index', compact('badWords'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|unique:bad_words,word|max:50',
        ]);

        BadWord::create(['word' => strtolower($validated['word'])]);

        return back()->with('success', 'Bad word added successfully!');
    }

    public function destroy(BadWord $badWord)
    {
        $badWord->delete();
        return back()->with('success', 'Bad word removed!');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'words' => 'required|string',
        ]);

        $words = array_filter(array_map('trim', explode(',', $validated['words'])));
        $added = 0;

        foreach ($words as $word) {
            $word = strtolower($word);
            if (!BadWord::where('word', $word)->exists()) {
                BadWord::create(['word' => $word]);
                $added++;
            }
        }

        return back()->with('success', "$added bad word(s) added!");
    }
}