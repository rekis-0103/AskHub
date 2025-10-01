<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Answer $answer)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:500',
        ]);

        $answer->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        auth()->user()->addXp(5);

        return back()->with('success', 'Comment added! +5 XP');
    }

    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted!');
    }
}
