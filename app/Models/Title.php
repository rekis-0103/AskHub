<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = ['name', 'description', 'required_level', 'color'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}