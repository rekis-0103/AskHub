<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Tag;
use Illuminate\Http\Request;

class PersonalDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $questions = $user->questions()->with('tags')->withCount('answers')->latest()->limit(10)->get();
        $answers = $user->answers()->with('question')->latest()->limit(10)->get();
        $bookmarks = $user->bookmarkedQuestions()->with('tags')->withCount('answers')->latest('bookmarks.created_at')->limit(10)->get();
        $followedQuestions = Question::query()->whereHas('followers', fn ($query) => $query->where('user_id', $user->id))
            ->with('tags')->withCount('answers')->latest('last_activity_at')->limit(10)->get();
        $followedTags = Tag::query()->whereHas('followers', fn ($query) => $query->where('user_id', $user->id))
            ->withCount('questions')->get();

        return view('dashboard', compact(
            'user', 'questions', 'answers', 'bookmarks', 'followedQuestions', 'followedTags'
        ));
    }
}
