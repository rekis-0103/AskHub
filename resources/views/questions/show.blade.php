<x-app-layout>
    <x-slot:title>{{ $question->title }}</x-slot:title>
    @push('meta')
        <link rel="canonical" href="{{ $question->public_url }}">
        <meta name="description" content="{{ Str::limit(strip_tags($question->body), 155) }}">
        <meta property="og:title" content="{{ $question->title }}">
        <meta property="og:url" content="{{ $question->public_url }}">
    @endpush

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if($question->duplicateOf)
            <div class="mb-5 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">This question is marked as a duplicate of <a class="font-semibold underline" href="{{ $question->duplicateOf->public_url }}">{{ $question->duplicateOf->title }}</a>.</div>
        @endif

        <header class="mb-6 border-b border-slate-200 pb-6">
            <div class="mb-3 flex flex-wrap items-center gap-2">
                @if($question->is_solved)<span class="rounded bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-800">Solved</span>@endif
                @if($question->status === 'closed')<span class="rounded bg-amber-50 px-2 py-1 text-xs font-bold text-amber-800">Closed</span>@endif
                @foreach($question->tags as $tag)<a href="{{ route('questions.index', ['tag' => $tag->slug]) }}" class="tag-chip">{{ $tag->name }}</a>@endforeach
            </div>
            <h1 class="max-w-5xl text-3xl font-bold leading-tight tracking-tight text-slate-950 sm:text-4xl">{{ $question->title }}</h1>
            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500"><span>Asked {{ $question->created_at->diffForHumans() }}</span><span>Active {{ ($question->last_activity_at ?? $question->updated_at)->diffForHumans() }}</span><span>{{ $question->views }} views</span></div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <main>
                <article class="grid grid-cols-[3rem_minmax(0,1fr)] gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-col items-center gap-2">
                        @auth
                            <form action="{{ route('questions.vote', $question) }}" method="POST">@csrf<input type="hidden" name="vote" value="1"><button class="grid h-9 w-9 place-items-center rounded border border-slate-300 text-lg hover:border-blue-500 hover:text-blue-700" aria-label="Upvote question">▲</button></form>
                        @endauth
                        <span class="font-mono text-lg font-bold">{{ $question->votes }}</span>
                        @auth
                            <form action="{{ route('questions.vote', $question) }}" method="POST">@csrf<input type="hidden" name="vote" value="-1"><button class="grid h-9 w-9 place-items-center rounded border border-slate-300 text-lg hover:border-blue-500 hover:text-blue-700" aria-label="Downvote question">▼</button></form>
                        @endauth
                    </div>
                    <div>
                        <div class="content-prose min-h-28">{!! $question->body_html !!}</div>
                        <div class="mt-7 flex flex-wrap items-end justify-between gap-4 border-t border-slate-100 pt-4">
                            <div class="flex flex-wrap gap-2">
                                @auth
                                    @if($viewerState['bookmarked'])<form method="POST" action="{{ route('bookmarks.destroy', $question) }}">@csrf @method('DELETE')<button class="quiet-button">Saved ✓</button></form>@else<form method="POST" action="{{ route('bookmarks.store', $question) }}">@csrf<button class="quiet-button">Save</button></form>@endif
                                    @if($viewerState['following'])<form method="POST" action="{{ route('questions.unfollow', $question) }}">@csrf @method('DELETE')<button class="quiet-button">Following ✓</button></form>@else<form method="POST" action="{{ route('questions.follow', $question) }}">@csrf<button class="quiet-button">Follow</button></form>@endif
                                    <div x-data="{ open: false }"><button @click="open = true" type="button" class="quiet-button">Report</button><div x-cloak x-show="open" @keydown.escape.window="open=false" class="fixed inset-0 z-50 grid place-items-center bg-slate-950/50 p-4"><form @click.outside="open=false" action="{{ route('questions.report', $question) }}" method="POST" class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">@csrf<h2 class="text-xl font-bold">Report question</h2><select name="category" class="mt-4 w-full rounded-md border-slate-300" required><option value="spam">Spam</option><option value="harassment">Harassment</option><option value="misinformation">Misinformation</option><option value="duplicate">Duplicate</option><option value="other">Other</option></select><textarea name="reason" rows="4" class="mt-3 w-full rounded-md border-slate-300" placeholder="Explain what should be reviewed" required></textarea><div class="mt-4 flex gap-2"><button class="primary-button">Submit report</button><button @click="open=false" type="button" class="quiet-button">Cancel</button></div></form></div></div>
                                @endauth
                                @can('update', $question)<a href="{{ route('questions.edit', $question) }}" class="quiet-button">Edit</a><form action="{{ route('questions.status', $question) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $question->status === 'open' ? 'closed' : 'open' }}"><button class="quiet-button">{{ $question->status === 'open' ? 'Close' : 'Reopen' }}</button></form>@endcan
                            </div>
                            <a href="{{ route('profile.show', $question->user) }}" class="rounded-md bg-blue-50 px-4 py-3 text-sm"><span class="block text-xs text-blue-700">asked by</span><strong class="text-slate-900">{{ $question->user->name }}</strong><span class="ml-2 text-xs text-slate-500">{{ $question->user->xp }} XP</span></a>
                        </div>
                    </div>
                </article>

                <section class="mt-8">
                    <h2 class="mb-4 text-2xl font-bold text-slate-950">{{ $question->answers->count() }} {{ Str::plural('answer', $question->answers->count()) }}</h2>
                    <div class="space-y-5">
                        @foreach($question->answers as $answer)
                            <article id="answer-{{ $answer->id }}" class="grid grid-cols-[3rem_minmax(0,1fr)] gap-4 rounded-lg border {{ $answer->is_best ? 'border-emerald-400 bg-emerald-50/30' : 'border-slate-200 bg-white' }} p-4 shadow-sm sm:p-6" x-data="{ report: false }" @keydown.escape.window="report=false">
                                <div class="flex flex-col items-center gap-2">
                                    @auth<form action="{{ route('answers.vote', $answer) }}" method="POST">@csrf<input type="hidden" name="vote" value="1"><button class="grid h-9 w-9 place-items-center rounded border border-slate-300" aria-label="Upvote answer">▲</button></form>@endauth
                                    <span class="font-mono text-lg font-bold">{{ $answer->votes }}</span>
                                    @auth<form action="{{ route('answers.vote', $answer) }}" method="POST">@csrf<input type="hidden" name="vote" value="-1"><button class="grid h-9 w-9 place-items-center rounded border border-slate-300" aria-label="Downvote answer">▼</button></form>@endauth
                                    @if($answer->is_best)<span title="Best answer" class="mt-2 text-2xl text-emerald-700">✓</span>@endif
                                </div>
                                <div>
                                    @if($answer->is_best)<p class="mb-4 text-sm font-bold text-emerald-800">Accepted answer</p>@endif
                                    <div class="content-prose">{!! $answer->body_html !!}</div>
                                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 text-sm">
                                        <div class="flex flex-wrap gap-2">
                                            @auth
                                                @if(auth()->user()->canSelectBestAnswer($question) && !$answer->is_best)<form action="{{ route('answers.markAsBest', $answer) }}" method="POST">@csrf<button class="quiet-button">Accept answer</button></form>@endif
                                                <button @click="report=true" class="quiet-button" type="button">Report</button>
                                            @endauth
                                            @can('update', $answer)<a href="{{ route('answers.edit', $answer) }}" class="quiet-button">Edit</a>@endcan
                                        </div>
                                        <a href="{{ route('profile.show', $answer->user) }}" class="font-semibold text-blue-700">{{ $answer->user->name }} <span class="font-normal text-slate-500">· {{ $answer->created_at->diffForHumans() }}</span></a>
                                    </div>

                                    <div class="mt-5 border-t border-slate-200 pt-4">
                                        @foreach($answer->comments as $comment)
                                            <div class="content-prose border-b border-slate-100 py-3 text-sm last:border-0">{!! $comment->body_html !!}<div class="text-xs text-slate-500">— <a href="{{ route('profile.show', $comment->user) }}">{{ $comment->user->name }}</a>, {{ $comment->created_at->diffForHumans() }} @can('delete', $comment)<form class="inline" action="{{ route('comments.destroy', $comment) }}" method="POST">@csrf @method('DELETE')<button class="ml-2 text-red-700">delete</button></form>@endcan</div></div>
                                        @endforeach
                                        @auth @if($question->status === 'open')<form action="{{ route('comments.store', $answer) }}" method="POST" class="mt-4 flex gap-2">@csrf<input name="body" maxlength="500" class="min-w-0 flex-1 rounded-md border-slate-300 text-sm" placeholder="Add a concise comment" required><button class="quiet-button">Comment</button></form>@endif @endauth
                                    </div>

                                    @auth<div x-cloak x-show="report" class="fixed inset-0 z-50 grid place-items-center bg-slate-950/50 p-4"><form @click.outside="report=false" action="{{ route('answers.report', $answer) }}" method="POST" class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">@csrf<h2 class="text-xl font-bold">Report answer</h2><select name="category" class="mt-4 w-full rounded-md border-slate-300" required><option value="spam">Spam</option><option value="harassment">Harassment</option><option value="misinformation">Misinformation</option><option value="other">Other</option></select><textarea name="reason" rows="4" class="mt-3 w-full rounded-md border-slate-300" required></textarea><div class="mt-4 flex gap-2"><button class="primary-button">Submit</button><button @click="report=false" type="button" class="quiet-button">Cancel</button></div></form></div>@endauth
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                @auth
                    @if($question->status === 'open')
                        <section class="mt-8 rounded-lg border border-slate-200 bg-white p-6"><h2 class="mb-5 text-xl font-bold">Your answer</h2><form action="{{ route('answers.store', $question) }}" method="POST" class="space-y-5">@csrf<x-markdown-editor name="body" label="Share a complete solution" :value="old('body', '')" rows="9" /><button class="primary-button">Post answer</button></form></section>
                    @else
                        <p class="mt-8 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">This question is closed and no longer accepts answers.</p>
                    @endif
                @else
                    <p class="mt-8 rounded-md border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900"><a href="{{ route('login') }}" class="font-semibold underline">Log in</a> to answer, vote, or follow this question.</p>
                @endauth
            </main>

            <aside class="space-y-6">
                @auth<section class="rounded-lg border border-slate-200 bg-white p-5"><h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Follow topics</h2><div class="mt-3 space-y-2">@foreach($question->tags as $tag)<div class="flex items-center justify-between gap-2"><a href="{{ route('questions.index', ['tag' => $tag->slug]) }}" class="tag-chip">{{ $tag->name }}</a>@if($viewerState['followed_tags']->contains($tag->id))<form method="POST" action="{{ route('tags.unfollow', $tag) }}">@csrf @method('DELETE')<button class="text-xs font-semibold text-slate-500 hover:text-red-700">Unfollow</button></form>@else<form method="POST" action="{{ route('tags.follow', $tag) }}">@csrf<button class="text-xs font-semibold text-blue-700">Follow</button></form>@endif</div>@endforeach</div></section>@endauth
                <section class="rounded-lg border border-slate-200 bg-white p-5"><h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Related questions</h2><div class="mt-3 space-y-4">@forelse($relatedQuestions as $related)<a href="{{ $related->public_url }}" class="block text-sm font-semibold leading-5 text-slate-800 hover:text-blue-700">{{ $related->title }}<span class="mt-1 block text-xs font-normal text-slate-500">{{ $related->answers_count }} answers</span></a>@empty<p class="text-sm text-slate-500">No related questions yet.</p>@endforelse</div></section>
            </aside>
        </div>
    </div>
</x-app-layout>
