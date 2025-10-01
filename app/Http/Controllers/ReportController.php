<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
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
            'reason' => 'required|string|min:10|max:500',
        ]);

        $existing = Report::where('user_id', auth()->id())
            ->where('reportable_type', get_class($reportable))
            ->where('reportable_id', $reportable->id)
            ->exists();

        if ($existing) {
            return back()->with('error', 'You have already reported this content.');
        }

        Report::create([
            'user_id' => auth()->id(),
            'reportable_type' => get_class($reportable),
            'reportable_id' => $reportable->id,
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Report submitted. Our team will review it.');
    }
}