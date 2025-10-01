<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function store(Request $request, Question $question)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:20',
        ]);

        $question->answers()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        auth()->user()->addXp(20);

        return back()->with('success', 'Answer posted successfully! +20 XP');
    }

    public function edit(Answer $answer)
    {
        if (auth()->id() !== $answer->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('answers.edit', compact('answer'));
    }

    public function update(Request $request, Answer $answer)
    {
        if (auth()->id() !== $answer->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|min:20',
        ]);

        $answer->update($validated);

        return redirect()->route('questions.show', $answer->question_id)
            ->with('success', 'Answer updated successfully!');
    }

    public function destroy(Answer $answer)
    {
        if (auth()->id() !== $answer->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $questionId = $answer->question_id;
        $answer->delete();

        return redirect()->route('questions.show', $questionId)
            ->with('success', 'Answer deleted successfully!');
    }

    public function markAsBest(Answer $answer)
    {
        $question = $answer->question;
        
        if (auth()->id() !== $question->user_id) {
            abort(403, 'Only question owner can select best answer.');
        }

        $answer->markAsBest();

        return back()->with('success', 'Best answer selected! User received +50 XP bonus!');
    }
}