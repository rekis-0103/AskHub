<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'xp',
        'level',
        'title_id',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Relationships
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    // Methods
    public function addXp(int $amount)
    {
        $this->xp += $amount;
        $this->checkLevelUp();
        $this->save();
    }

    private function checkLevelUp()
    {
        $nextLevel = Level::where('level', $this->level + 1)->first();
        
        if ($nextLevel && $this->xp >= $nextLevel->xp_required) {
            $this->level = $nextLevel->level;
            $this->checkLevelUp(); // Recursive check
        }
    }

    public function canSelectBestAnswer(Question $question)
    {
        return $this->id === $question->user_id;
    }

    public function hasVoted($votable)
    {
        return $this->votes()
            ->where('votable_type', get_class($votable))
            ->where('votable_id', $votable->id)
            ->first();
    }
}