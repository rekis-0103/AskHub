<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Services\ContentNotificationService;
use App\Services\GamificationService;
use App\Services\QuestionWorkflowService;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function store(
        Request $request,
        Question $question,
        GamificationService $gamification,
        ContentNotificationService $notifications,
    ) {
        abort_if($question->status === 'closed', 422, 'This question is closed.');

        $validated = $request->validate([
            'body' => 'required|string|min:20',
        ]);

        $answer = $question->answers()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);
        $question->update(['last_activity_at' => now()]);

        $awarded = $gamification->award(auth()->user(), 'answer_created', $answer);
        $notifications->answerCreated($answer->load(['question.tags', 'user']));

        return back()->with('success', 'Answer posted successfully!'.($awarded ? ' +20 XP' : ''));
    }

    public function edit(Answer $answer)
    {
        $this->authorize('update', $answer);

        return view('answers.edit', compact('answer'));
    }

    public function update(Request $request, Answer $answer)
    {
        $this->authorize('update', $answer);

        $validated = $request->validate([
            'body' => 'required|string|min:20',
        ]);

        $answer->update($validated);
        $answer->question()->update(['last_activity_at' => now()]);

        return redirect($answer->question->public_url)
            ->with('success', 'Answer updated successfully!');
    }

    public function destroy(Answer $answer)
    {
        $this->authorize('delete', $answer);

        $question = $answer->question;
        $answer->delete();
        $question->update(['last_activity_at' => now()]);

        return redirect($question->public_url)
            ->with('success', 'Answer deleted successfully!');
    }

    public function markAsBest(Answer $answer, QuestionWorkflowService $workflow)
    {
        $changed = $workflow->markBest($answer, auth()->user());

        return back()->with('success', $changed
            ? 'Best answer selected! The author received a one-time +50 XP bonus.'
            : 'This answer is already selected as best.');
    }
}
