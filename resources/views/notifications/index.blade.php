<x-app-layout>
    <x-slot:title>Notifications</x-slot:title>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <header class="mb-6 flex items-end justify-between"><div><p class="font-mono text-xs font-semibold uppercase tracking-widest text-blue-700">Inbox</p><h1 class="mt-1 text-3xl font-bold">Notifications</h1></div><form method="POST" action="{{ route('notifications.readAll') }}">@csrf<button class="quiet-button">Mark all read</button></form></header>
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.read', $notification) }}" class="block border-b border-slate-200 p-5 last:border-0 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/60' }}"><p class="font-medium text-slate-800">{{ $notification->data['message'] ?? 'New activity' }}</p><p class="mt-1 text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</p></a>
            @empty<div class="p-12 text-center text-slate-500">No notifications yet.</div>@endforelse
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-app-layout>
