<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Question;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class FollowController extends Controller
{
    public function storeQuestion(Question $question)
    {
        return $this->store($question, 'Question followed.');
    }

    public function destroyQuestion(Question $question)
    {
        return $this->destroy($question, 'Question unfollowed.');
    }

    public function storeTag(Tag $tag)
    {
        return $this->store($tag, 'Tag followed.');
    }

    public function destroyTag(Tag $tag)
    {
        return $this->destroy($tag, 'Tag unfollowed.');
    }

    private function store(Model $followable, string $message)
    {
        Follow::firstOrCreate([
            'user_id' => auth()->id(),
            'followable_type' => $followable::class,
            'followable_id' => $followable->getKey(),
        ]);

        return back()->with('success', $message);
    }

    private function destroy(Model $followable, string $message)
    {
        Follow::query()->where('user_id', auth()->id())
            ->where('followable_type', $followable::class)
            ->where('followable_id', $followable->getKey())->delete();

        return back()->with('success', $message);
    }
}
