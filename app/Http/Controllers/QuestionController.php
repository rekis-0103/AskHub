<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with(['user.title'])
            ->withCount('answers')
            ->latest()
            ->paginate(20);

        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
        ]);

        $question = auth()->user()->questions()->create($validated);
        auth()->user()->addXp(10);

        return redirect()->route('questions.show', $question)
            ->with('success', 'Question posted successfully! +10 XP');
    }

    public function show(Question $question)
    {
        $question->increment('views');
        $question->load([
            'user.title',
            'answers.user.title',
            'answers.comments.user.title',
            'bestAnswer'
        ]);

        return view('questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        if (auth()->id() !== $question->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        if (auth()->id() !== $question->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
        ]);

        $question->update($validated);

        return redirect()->route('questions.show', $question)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        if (auth()->id() !== $question->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $question->delete();

        return redirect()->route('questions.index')
            ->with('success', 'Question deleted successfully!');
    }
}