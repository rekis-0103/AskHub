<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadWord extends Model
{
    protected $fillable = ['word'];

    public static function filter($text)
    {
        $badWords = self::all()->pluck('word')->toArray();
        
        foreach ($badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            $replacement = str_repeat('#', mb_strlen($word));
            $text = preg_replace($pattern, $replacement, $text);
        }
        
        return $text;
    }
}