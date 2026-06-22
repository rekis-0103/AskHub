<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Tag;
use App\Services\GamificationService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'tag' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'in:all,unanswered,answered,solved,open,closed'],
            'sort' => ['nullable', 'in:newest,active,votes'],
        ]);

        $questions = Question::with(['user.title'])
            ->with('tags')
            ->withCount('answers')
            ->search($filters['q'] ?? null)
            ->when($filters['tag'] ?? null,
                fn ($query, $tag) => $query->whereHas('tags', fn ($query) => $query->where('slug', $tag)))
            ->when(($filters['status'] ?? 'all') === 'unanswered', fn ($query) => $query->doesntHave('answers'))
            ->when(($filters['status'] ?? 'all') === 'answered', fn ($query) => $query->has('answers'))
            ->when(($filters['status'] ?? 'all') === 'solved', fn ($query) => $query->whereNotNull('best_answer_id'))
            ->when(in_array($filters['status'] ?? '', ['open', 'closed'], true),
                fn ($query) => $query->where('status', $filters['status']))
            ->when(($filters['sort'] ?? 'newest') === 'votes', fn ($query) => $query->orderByDesc('votes'))
            ->when(($filters['sort'] ?? 'newest') === 'active', fn ($query) => $query->orderByDesc('last_activity_at'))
            ->when(($filters['sort'] ?? 'newest') === 'newest', fn ($query) => $query->latest())
            ->paginate(20)
            ->withQueryString();

        $popularTags = Tag::query()->withCount('questions')
            ->orderByDesc('questions_count')->limit(12)->get();

        return view('questions.index', compact('questions', 'popularTags', 'filters'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request, TagService $tags, GamificationService $gamification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
            'tags' => ['required', 'string', 'max:300'],
        ]);

        $question = auth()->user()->questions()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);
        $tags->sync($question, $this->parseTags($validated['tags']));
        $awarded = $gamification->award(auth()->user(), 'question_created', $question);

        return redirect($question->public_url)
            ->with('success', 'Question posted successfully!'.($awarded ? ' +10 XP' : ''));
    }

    public function show(Request $request, Question $question, ?string $slug = null)
    {
        if ($slug !== $question->slug) {
            return redirect($question->public_url, 301);
        }

        $viewKey = "question_viewed_{$question->id}";
        if ((! auth()->check() || auth()->id() !== $question->user_id) && ! $request->session()->has($viewKey)) {
            $question->increment('views');
            $request->session()->put($viewKey, now()->timestamp);
        }

        $question->load([
            'user.title',
            'tags',
            'answers.user.title',
            'answers.comments.user.title',
            'bestAnswer',
            'duplicateOf',
        ]);
        $question->setRelation('answers', $question->answers
            ->sortByDesc(fn ($answer) => [$answer->is_best, $answer->votes, -$answer->id])
            ->values());

        $relatedQuestions = Question::query()
            ->whereKeyNot($question->id)
            ->whereHas('tags', fn ($query) => $query->whereIn('tags.id', $question->tags->pluck('id')))
            ->with('tags')->withCount('answers')
            ->orderByDesc('last_activity_at')->limit(5)->get();

        $viewerState = ['bookmarked' => false, 'following' => false, 'followed_tags' => collect()];
        if (auth()->check()) {
            $viewerState = [
                'bookmarked' => $question->bookmarks()->where('user_id', auth()->id())->exists(),
                'following' => $question->followers()->where('user_id', auth()->id())->exists(),
                'followed_tags' => auth()->user()->follows()
                    ->where('followable_type', Tag::class)
                    ->whereIn('followable_id', $question->tags->pluck('id'))
                    ->pluck('followable_id'),
            ];
        }

        return view('questions.show', compact('question', 'relatedQuestions', 'viewerState'));
    }

    public function edit(Question $question)
    {
        $this->authorize('update', $question);

        $question->load('tags');

        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question, TagService $tags)
    {
        $this->authorize('update', $question);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:20',
            'tags' => ['required', 'string', 'max:300'],
        ]);

        $question->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'last_activity_at' => now(),
        ]);
        $tags->sync($question, $this->parseTags($validated['tags']));

        return redirect($question->public_url)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $this->authorize('delete', $question);

        $question->delete();

        return redirect()->route('questions.index')
            ->with('success', 'Question deleted successfully!');
    }

    public function updateStatus(Request $request, Question $question)
    {
        $this->authorize('update', $question);
        $validated = $request->validate(['status' => ['required', 'in:open,closed']]);
        $question->update(['status' => $validated['status'], 'last_activity_at' => now()]);

        return back()->with('success', "Question marked {$validated['status']}.");
    }

    public function suggestions(Request $request)
    {
        $validated = $request->validate(['q' => ['required', 'string', 'min:4', 'max:100']]);

        return Question::query()->search($validated['q'])
            ->select(['id', 'title', 'slug', 'best_answer_id'])
            ->orderByDesc('votes')->limit(5)->get()
            ->map(fn (Question $question) => [
                'title' => $question->title,
                'url' => $question->public_url,
                'solved' => $question->is_solved,
            ]);
    }

    private function parseTags(string $input): array
    {
        $tags = collect(explode(',', $input))->map(fn ($tag) => trim($tag))->filter()->unique()->values();

        if ($tags->isEmpty() || $tags->count() > 5 || $tags->contains(
            fn ($tag) => mb_strlen($tag) > 50 || ! preg_match('/^[\pL\pN][\pL\pN +.#_-]*$/u', $tag)
        )) {
            throw ValidationException::withMessages([
                'tags' => 'Add 1–5 comma-separated tags using letters, numbers, spaces, +, ., #, _ or -.',
            ]);
        }

        return $tags->all();
    }
}
