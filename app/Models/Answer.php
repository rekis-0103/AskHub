<?php

namespace App\Models;

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

    // Methods
    public function markAsBest()
    {
        $question = $this->question;
        
        // Remove previous best answer
        if ($question->bestAnswer) {
            $question->bestAnswer->update(['is_best' => false]);
        }
        
        // Set new best answer
        $this->is_best = true;
        $this->save();
        
        $question->best_answer_id = $this->id;
        $question->save();
        
        // Award bonus XP
        $this->user->addXp(50);
    }
}