<?php

namespace App\Providers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Comment;
use App\Policies\QuestionPolicy;
use App\Policies\AnswerPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Question::class => QuestionPolicy::class,
        Answer::class => AnswerPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}