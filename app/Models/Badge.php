<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'criteria_type', 'criteria_value',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('awarded_at');
    }
}
