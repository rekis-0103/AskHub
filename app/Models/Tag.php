<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function followers()
    {
        return $this->morphMany(Follow::class, 'followable');
    }
}
