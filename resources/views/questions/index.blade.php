<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <header class="mb-7 border-b border-slate-200 pb-6">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="mb-1 font-mono text-xs font-semibold uppercase tracking-widest text-blue-700">Community knowledge</p>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-950">Find the question worth answering</h1>
                    <p class="mt-2 max-w-2xl text-slate-600">Search first, narrow by topic, then add what the community is missing.</p>
                </div>
                @auth<a href="{{ route('questions.create') }}" class="primary-button justify-center">Ask a question</a>@endauth
            </div>

            <form action="{{ route('questions.index') }}" method="GET" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-3 shadow-sm md:grid-cols-[minmax(0,1fr)_10rem_10rem_auto]">
                <label class="sr-only" for="q">Search questions</label>
                <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search titles and details…"
                    class="rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600">
                <select name="status" aria-label="Question status" class="rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600">
                    @foreach(['all' => 'All status', 'unanswered' => 'Unanswered', 'answered' => 'Answered', 'solved' => 'Solved', 'open' => 'Open', 'closed' => 'Closed'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="sort" aria-label="Sort questions" class="rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600">
                    @foreach(['newest' => 'Newest', 'active' => 'Recently active', 'votes' => 'Highest votes'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['sort'] ?? 'newest') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @if($filters['tag'] ?? null)<input type="hidden" name="tag" value="{{ $filters['tag'] }}">@endif
                <button class="primary-button justify-center">Search</button>
            </form>
        </header>

        <div class="grid gap-8 lg:grid-cols-[13rem_minmax(0,1fr)]">
            <aside>
                <div class="sticky top-24">
                    <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Browse tags</h2>
                    <div class="flex flex-wrap gap-2 lg:flex-col lg:items-start">
                        <a href="{{ route('questions.index', request()->except('tag', 'page')) }}" class="text-sm {{ empty($filters['tag']) ? 'font-semibold text-blue-700' : 'text-slate-600 hover:text-slate-900' }}">All topics</a>
                        @foreach($popularTags as $tag)
                            <a href="{{ route('questions.index', array_merge(request()->except('page'), ['tag' => $tag->slug])) }}" class="flex items-center gap-2 text-sm {{ ($filters['tag'] ?? null) === $tag->slug ? 'font-semibold text-blue-700' : 'text-slate-600 hover:text-slate-900' }}">
                                <span class="font-mono">{{ $tag->name }}</span><span class="text-xs text-slate-400">{{ $tag->questions_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <section aria-label="Question results">
                <div class="mb-3 flex items-center justify-between text-sm text-slate-500">
                    <span>{{ $questions->total() }} {{ Str::plural('question', $questions->total()) }}</span>
                    @if(request()->hasAny(['q', 'tag', 'status', 'sort']))<a href="{{ route('questions.index') }}" class="font-medium text-blue-700">Clear filters</a>@endif
                </div>
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    @forelse($questions as $question)
                        <article class="relative grid gap-4 border-b border-slate-200 p-5 last:border-b-0 sm:grid-cols-[5.5rem_minmax(0,1fr)]">
                            <div class="flex gap-4 text-xs text-slate-500 sm:flex-col sm:gap-1 sm:text-right">
                                <span><strong class="text-slate-800">{{ $question->votes }}</strong> votes</span>
                                <span class="{{ $question->is_solved ? 'font-semibold text-emerald-700' : '' }}"><strong>{{ $question->answers_count }}</strong> answers</span>
                                <span>{{ $question->views }} views</span>
                            </div>
                            <div>
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    @if($question->is_solved)<span class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-800">Solved</span>@endif
                                    @if($question->status === 'closed')<span class="rounded bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800">Closed</span>@endif
                                </div>
                                <a href="{{ $question->public_url }}" class="text-lg font-bold leading-snug text-slate-900 hover:text-blue-700">{{ $question->title }}</a>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ Str::limit($question->body, 220) }}</p>
                                <div class="mt-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                                    <div class="flex flex-wrap gap-2">@foreach($question->tags as $tag)<a href="{{ route('questions.index', ['tag' => $tag->slug]) }}" class="tag-chip">{{ $tag->name }}</a>@endforeach</div>
                                    <p class="text-xs text-slate-500"><a href="{{ route('profile.show', $question->user) }}" class="font-semibold text-slate-700 hover:text-blue-700">{{ $question->user->name }}</a> asked {{ $question->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="p-12 text-center"><h2 class="font-semibold text-slate-800">No questions match these filters</h2><p class="mt-1 text-sm text-slate-500">Try fewer filters or ask a new question.</p></div>
                    @endforelse
                </div>
                <div class="mt-6">{{ $questions->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
