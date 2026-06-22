<?php

namespace App\Notifications;

use App\Models\Question;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ContentActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $activity,
        public User $actor,
        public Question $question,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'activity' => $this->activity,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'question_id' => $this->question->id,
            'question_slug' => $this->question->slug,
            'question_title' => $this->question->title,
            'message' => $this->message,
        ];
    }
}
