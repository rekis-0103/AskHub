<?php

namespace App\Models;

use App\Services\MarkdownRenderer;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['question_id', 'user_id', 'body', 'votes', 'is_best'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($answer) {
            $answer->body = BadWord::filter($answer->body);
        });

        static::updating(function ($answer) {
            $answer->body = BadWord::filter($answer->body);
        });
    }

    // Relationships
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getBodyHtmlAttribute(): string
    {
        return app(MarkdownRenderer::class)->render($this->body);
    }

    // Methods
}
