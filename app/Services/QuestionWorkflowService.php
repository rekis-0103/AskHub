<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuestionWorkflowService
{
    public function __construct(
        private GamificationService $gamification,
        private ContentNotificationService $notifications,
    ) {}

    public function markBest(Answer $answer, User $actor): bool
    {
        $changed = DB::transaction(function () use ($answer, $actor) {
            $question = Question::query()->lockForUpdate()->findOrFail($answer->question_id);

            abort_unless($actor->id === $question->user_id, 403,
                'Only the question owner can select the best answer.');

            if ($question->best_answer_id === $answer->id) {
                return false;
            }

            $question->answers()->where('is_best', true)->update(['is_best' => false]);
            $answer->update(['is_best' => true]);
            $question->update([
                'best_answer_id' => $answer->id,
                'last_activity_at' => now(),
            ]);

            return true;
        });

        if ($changed) {
            $this->gamification->award($answer->user, 'best_answer', $answer);
            $this->notifications->bestAnswerSelected($answer->fresh('question'), $actor);
        }

        return $changed;
    }
}
