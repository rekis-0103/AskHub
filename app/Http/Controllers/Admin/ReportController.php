<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,reviewed,resolved'],
            'category' => ['nullable', 'in:spam,harassment,misinformation,duplicate,other'],
        ]);
        $reports = Report::with(['user', 'reportable'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.reports.index', compact('reports', 'filters'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'reportable.user', 'resolver']);

        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
            'resolution_note' => 'nullable|string|max:1000',
        ]);

        $report->update([
            ...$validated,
            'resolved_by' => $validated['status'] === 'resolved' ? auth()->id() : null,
        ]);
        $this->audit('report.status_updated', $report, $validated);

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
            $subjectType = $reportable::class;
            $subjectId = $reportable->id;
            $reportable->delete();
            $report->update(['status' => 'resolved', 'resolved_by' => auth()->id()]);
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'reported_content.deleted',
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'metadata' => ['report_id' => $report->id],
            ]);

            return back()->with('success', 'Content deleted and report resolved!');
        }

        return back()->with('error', 'Content not found!');
    }

    private function audit(string $action, Report $report, array $metadata = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $report::class,
            'subject_id' => $report->id,
            'metadata' => $metadata,
        ]);
    }
}
