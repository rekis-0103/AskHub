<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use App\Services\ContentNotificationService;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(
        Request $request,
        Answer $answer,
        GamificationService $gamification,
        ContentNotificationService $notifications,
    ) {
        abort_if($answer->question->status === 'closed', 422, 'This question is closed.');

        $validated = $request->validate([
            'body' => 'required|string|max:500',
        ]);

        $comment = $answer->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        $answer->question()->update(['last_activity_at' => now()]);
        $awarded = $gamification->award(auth()->user(), 'comment_created', $comment);
        $notifications->commentCreated($answer->load(['question.tags', 'user']), $comment->body, auth()->user());

        return back()->with('success', 'Comment added!'.($awarded ? ' +5 XP' : ''));
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Comment deleted!');
    }
}
