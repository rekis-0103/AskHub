<?php

namespace App\Http\Controllers;

use App\Models\User;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaders = User::query()->with(['title', 'badges'])
            ->withCount(['answers', 'questions'])
            ->whereNull('suspended_at')
            ->orderByDesc('xp')->orderBy('name')->paginate(50);

        return view('leaderboard.index', compact('leaders'));
    }
}
