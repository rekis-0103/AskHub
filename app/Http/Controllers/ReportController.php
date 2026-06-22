<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reportQuestion(Request $request, Question $question)
    {
        return $this->handleReport($request, $question);
    }

    public function reportAnswer(Request $request, Answer $answer)
    {
        return $this->handleReport($request, $answer);
    }

    private function handleReport(Request $request, $reportable)
    {
        $validated = $request->validate([
            'category' => 'required|in:spam,harassment,misinformation,duplicate,other',
            'reason' => 'required|string|min:10|max:500',
        ]);

        abort_if($reportable->user_id === auth()->id(), 422, 'You cannot report your own content.');

        $report = Report::firstOrCreate([
            'user_id' => auth()->id(),
            'reportable_type' => $reportable::class,
            'reportable_id' => $reportable->id,
        ], [
            'category' => $validated['category'],
            'reason' => $validated['reason'],
        ]);

        if (! $report->wasRecentlyCreated) {
            return back()->with('error', 'You have already reported this content.');
        }

        return back()->with('success', 'Report submitted. Our team will review it.');
    }
}
