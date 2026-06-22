<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['title', 'questions', 'answers']);
        $user->load('badges');

        $stats = [
            'questions_count' => $user->questions()->count(),
            'answers_count' => $user->answers()->count(),
            'best_answers_count' => $user->answers()->where('is_best', true)->count(),
            'total_votes' => $user->questions()->sum('votes') + $user->answers()->sum('votes'),
        ];

        $availableTitles = Title::where('required_level', '<=', $user->level)->get();

        return view('profile.show', compact('user', 'stats', 'availableTitles'));
    }

    public function updateTitle(Request $request)
    {
        $validated = $request->validate([
            'title_id' => [
                'nullable',
                'exists:titles,id',
            ],
        ]);

        if ($validated['title_id']) {
            $title = Title::find($validated['title_id']);
            if ($title->required_level > auth()->user()->level) {
                return back()->with('error', 'You have not unlocked this title yet.');
            }
        }

        auth()->user()->update(['title_id' => $validated['title_id']]);

        return back()->with('success', 'Title updated successfully!');
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->id())],
            'username' => [
                'sometimes', 'required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_.-]+$/',
                Rule::unique('users')->ignore(auth()->id()),
            ],
        ]);

        $user = auth()->user();
        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;
        $user->fill($validated);
        if ($emailChanged) {
            $user->email_verified_at = null;
        }
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
