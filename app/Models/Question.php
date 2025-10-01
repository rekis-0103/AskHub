<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['user_id', 'title', 'body', 'votes', 'views', 'best_answer_id'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($question) {
            $question->title = BadWord::filter($question->title);
            $question->body = BadWord::filter($question->body);
        });

        static::updating(function ($question) {
            $question->title = BadWord::filter($question->title);
            $question->body = BadWord::filter($question->body);
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function bestAnswer()
    {
        return $this->belongsTo(Answer::class, 'best_answer_id');
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }
}