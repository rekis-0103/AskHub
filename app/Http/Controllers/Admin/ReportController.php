<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'reportable'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'reportable']);
        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
        ]);

        $report->update($validated);

        return back()->with('success', 'Report status updated!');
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted!');
    }

    public function deleteReportable(Report $report)
    {
        $reportable = $report->reportable;
        
        if ($reportable) {
            $reportable->delete();
            $report->update(['status' => 'resolved']);
            
            return back()->with('success', 'Content deleted and report resolved!');
        }

        return back()->with('error', 'Content not found!');
    }
}