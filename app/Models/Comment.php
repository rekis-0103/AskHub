<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['answer_id', 'user_id', 'body'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($comment) {
            $comment->body = BadWord::filter($comment->body);
        });

        static::updating(function ($comment) {
            $comment->body = BadWord::filter($comment->body);
        });
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}