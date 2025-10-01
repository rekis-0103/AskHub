<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_reports' => Report::where('status', 'pending')->count(),
            'total_questions' => Question::count(),
            'total_answers' => Answer::count(),
            'total_users' => User::count(),
        ];

        $recentReports = Report::with(['user', 'reportable'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports'));
    }
}