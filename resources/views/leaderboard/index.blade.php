<x-app-layout>
    <x-slot:title>Leaderboard</x-slot:title>
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <header class="mb-7"><p class="font-mono text-xs font-semibold uppercase tracking-widest text-blue-700">Community reputation</p><h1 class="mt-1 text-3xl font-bold">Leaderboard</h1><p class="mt-2 text-slate-600">Recognition for sustained, useful participation—not one-off activity.</p></header>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            @foreach($leaders as $leader)
                <div class="grid grid-cols-[3rem_minmax(0,1fr)_auto] items-center gap-4 border-b border-slate-200 p-4 last:border-0">
                    <span class="font-mono text-lg font-bold text-slate-400">{{ ($leaders->currentPage() - 1) * $leaders->perPage() + $loop->iteration }}</span>
                    <div><a href="{{ route('profile.show', $leader) }}" class="font-bold text-slate-900 hover:text-blue-700">{{ $leader->name }}</a><p class="text-xs text-slate-500">{{ $leader->answers_count }} answers · {{ $leader->questions_count }} questions</p><div class="mt-2 flex flex-wrap gap-1">@foreach($leader->badges as $badge)<span title="{{ $badge->description }}" class="rounded bg-amber-50 px-2 py-0.5 text-xs text-amber-900">{{ $badge->icon }} {{ $badge->name }}</span>@endforeach</div></div>
                    <div class="text-right"><strong class="font-mono text-xl text-blue-700">{{ number_format($leader->xp) }}</strong><span class="block text-xs text-slate-500">XP</span></div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $leaders->links() }}</div>
    </div>
</x-app-layout>
