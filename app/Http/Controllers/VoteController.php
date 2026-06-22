<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        abort_if($votable->user_id === $user->id, 422, 'You cannot vote on your own content.');

        $message = DB::transaction(function () use ($user, $votable, $validated) {
            $existingVote = Vote::query()
                ->where('user_id', $user->id)
                ->where('votable_type', $votable::class)
                ->where('votable_id', $votable->id)
                ->lockForUpdate()
                ->first();

            if ($existingVote?->vote === (int) $validated['vote']) {
                $existingVote->delete();
                $message = 'Vote removed!';
            } elseif ($existingVote) {
                $existingVote->update(['vote' => $validated['vote']]);
                $message = 'Vote updated!';
            } else {
                Vote::create([
                    'user_id' => $user->id,
                    'votable_type' => $votable::class,
                    'votable_id' => $votable->id,
                    'vote' => $validated['vote'],
                ]);
                $message = 'Vote recorded!';
            }

            $total = (int) Vote::query()
                ->where('votable_type', $votable::class)
                ->where('votable_id', $votable->id)
                ->sum('vote');
            $votable->update(['votes' => $total]);

            return $message;
        });

        return back()->with('success', $message);
    }
}
