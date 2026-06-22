<x-app-layout>
    <x-slot:title>My activity</x-slot:title>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <header class="mb-7"><p class="font-mono text-xs font-semibold uppercase tracking-widest text-blue-700">Personal workspace</p><h1 class="mt-1 text-3xl font-bold text-slate-950">Your AskHub activity</h1><p class="mt-2 text-slate-600">{{ $user->xp }} XP · Level {{ $user->level }} · {{ $user->unreadNotifications()->count() }} unread notifications</p></header>
        <div class="grid gap-6 lg:grid-cols-2">
            @php
                $sections = [
                    ['title' => 'Your questions', 'items' => $questions, 'empty' => 'You have not asked a question yet.'],
                    ['title' => 'Saved questions', 'items' => $bookmarks, 'empty' => 'Save useful questions to find them here.'],
                    ['title' => 'Followed questions', 'items' => $followedQuestions, 'empty' => 'Follow a question to receive updates.'],
                ];
            @endphp
            @foreach($sections as $section)
                <section class="rounded-lg border border-slate-200 bg-white p-5"><h2 class="mb-4 text-lg font-bold">{{ $section['title'] }}</h2><div class="space-y-4">@forelse($section['items'] as $question)<a href="{{ $question->public_url }}" class="block"><span class="font-semibold text-slate-800 hover:text-blue-700">{{ $question->title }}</span><span class="mt-1 block text-xs text-slate-500">{{ $question->answers_count }} answers · {{ $question->votes }} votes</span></a>@empty<p class="text-sm text-slate-500">{{ $section['empty'] }}</p>@endforelse</div></section>
            @endforeach
            <section class="rounded-lg border border-slate-200 bg-white p-5"><h2 class="mb-4 text-lg font-bold">Recent answers</h2><div class="space-y-4">@forelse($answers as $answer)<a href="{{ $answer->question->public_url }}#answer-{{ $answer->id }}" class="block"><span class="font-semibold text-slate-800 hover:text-blue-700">{{ $answer->question->title }}</span><span class="mt-1 block text-xs text-slate-500">{{ Str::limit($answer->body, 90) }}</span></a>@empty<p class="text-sm text-slate-500">You have not posted an answer yet.</p>@endforelse</div></section>
        </div>
        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5"><h2 class="mb-3 text-lg font-bold">Followed tags</h2><div class="flex flex-wrap gap-2">@forelse($followedTags as $tag)<a href="{{ route('questions.index', ['tag' => $tag->slug]) }}" class="tag-chip">{{ $tag->name }} · {{ $tag->questions_count }}</a>@empty<p class="text-sm text-slate-500">Follow tags from a question page.</p>@endforelse</div></section>
    </div>
</x-app-layout>
