<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function voteQuestion(Request $request, Question $question)
    {
        return $this->handleVote($request, $question);
    }

    public function voteAnswer(Request $request, Answer $answer)
    {
        return $this->handleVote($request, $answer);
    }

    private function handleVote(Request $request, $votable)
    {
        $validated = $request->validate([
            'vote' => 'required|in:1,-1',
        ]);

        $user = auth()->user();
        
        $existingVote = Vote::where('user_id', $user->id)
            ->where('votable_type', get_class($votable))
            ->where('votable_id', $votable->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote == $validated['vote']) {
                $existingVote->delete();
                $votable->decrement('votes', $validated['vote']);
                return back()->with('success', 'Vote removed!');
            }
            
            $existingVote->update(['vote' => $validated['vote']]);
            $votable->increment('votes', $validated['vote'] * 2);
            return back()->with('success', 'Vote updated!');
        }

        Vote::create([
            'user_id' => $user->id,
            'votable_type' => get_class($votable),
            'votable_id' => $votable->id,
            'vote' => $validated['vote'],
        ]);

        $votable->increment('votes', $validated['vote']);

        return back()->with('success', 'Vote recorded!');
    }
}