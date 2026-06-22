<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Notifications\ContentActivityNotification;
use Illuminate\Support\Collection;

class ContentNotificationService
{
    public function answerCreated(Answer $answer): void
    {
        $question = $answer->question;
        $recipients = $this->questionFollowers($question)
            ->push($question->user)
            ->merge($this->mentionedUsers($answer->body));

        $this->send($recipients, $answer->user, $question, 'answer',
            "{$answer->user->name} answered “{$question->title}”.");
    }

    public function commentCreated(Answer $answer, string $body, User $actor): void
    {
        $question = $answer->question;
        $recipients = $this->questionFollowers($question)
            ->push($question->user)
            ->push($answer->user)
            ->merge($this->mentionedUsers($body));

        $this->send($recipients, $actor, $question, 'comment',
            "{$actor->name} commented on “{$question->title}”.");
    }

    public function bestAnswerSelected(Answer $answer, User $actor): void
    {
        $this->send(collect([$answer->user]), $actor, $answer->question, 'best_answer',
            "Your answer to “{$answer->question->title}” was selected as the best answer.");
    }

    private function questionFollowers(Question $question): Collection
    {
        return User::query()
            ->whereHas('follows', fn ($query) => $query
                ->where('followable_type', Question::class)
                ->where('followable_id', $question->id))
            ->orWhereHas('follows', fn ($query) => $query
                ->where('followable_type', \App\Models\Tag::class)
                ->whereIn('followable_id', $question->tags()->select('tags.id')))
            ->get();
    }

    private function mentionedUsers(string $body): Collection
    {
        preg_match_all('/@([A-Za-z0-9_.-]{2,50})/', $body, $matches);

        return empty($matches[1])
            ? collect()
            : User::query()->whereIn('username', array_unique($matches[1]))->get();
    }

    private function send(Collection $recipients, User $actor, Question $question, string $activity, string $message): void
    {
        $recipients->unique('id')
            ->reject(fn (User $user) => $user->is($actor))
            ->each(fn (User $user) => $user->notify(
                new ContentActivityNotification($activity, $actor, $question, $message)
            ));
    }
}
