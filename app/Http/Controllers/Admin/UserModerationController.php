<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class UserModerationController extends Controller
{
    public function suspend(Request $request, User $user)
    {
        abort_if($user->is_admin, 422, 'Administrators cannot be suspended.');
        $validated = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:500']]);
        $user->update(['suspended_at' => now()]);
        $this->audit('user.suspended', $user, $validated);

        return back()->with('success', 'User suspended.');
    }

    public function restore(User $user)
    {
        $user->update(['suspended_at' => null]);
        $this->audit('user.restored', $user);

        return back()->with('success', 'User access restored.');
    }

    private function audit(string $action, User $subject, array $metadata = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->id,
            'metadata' => $metadata,
        ]);
    }
}
