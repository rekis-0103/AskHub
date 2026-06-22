<?php

namespace App\Models;

use App\Services\MarkdownRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Question extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'body', 'votes', 'views', 'best_answer_id',
        'status', 'last_activity_at', 'duplicate_of_id',
    ];

    protected function casts(): array
    {
        return ['last_activity_at' => 'datetime'];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($question) {
            $question->title = BadWord::filter($question->title);
            $question->body = BadWord::filter($question->body);
            $question->last_activity_at ??= now();
        });

        static::created(function ($question) {
            if (! $question->slug) {
                $question->updateQuietly([
                    'slug' => Str::slug($question->title).'-'.$question->id,
                ]);
            }
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

    public function duplicateOf()
    {
        return $this->belongsTo(self::class, 'duplicate_of_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function followers()
    {
        return $this->morphMany(Follow::class, 'followable');
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

    public function getBodyHtmlAttribute(): string
    {
        return app(MarkdownRenderer::class)->render($this->body);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('questions.show', ['question' => $this, 'slug' => $this->slug]);
    }

    public function getIsSolvedAttribute(): bool
    {
        return $this->best_answer_id !== null;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $escaped = addcslashes($term, '%_\\');

        return $query->where(function (Builder $query) use ($escaped) {
            $query->where('title', 'like', "%{$escaped}%")
                ->orWhere('body', 'like', "%{$escaped}%");
        });
    }
}
