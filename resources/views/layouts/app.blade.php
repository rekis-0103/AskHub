<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · '.config('app.name', 'AskHub') : config('app.name', 'AskHub') }}</title>
    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f7fb] antialiased">
    <nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold tracking-tight text-slate-900">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-blue-700 font-mono text-sm text-white">AH</span>
                    <span class="text-xl">AskHub</span>
                </a>
                <div class="hidden items-center gap-6 md:flex">
                    <a href="{{ route('questions.index') }}" class="text-sm font-medium {{ request()->routeIs('questions.*') ? 'text-blue-700' : 'text-slate-600 hover:text-slate-900' }}">Questions</a>
                    <a href="{{ route('leaderboard.index') }}" class="text-sm font-medium {{ request()->routeIs('leaderboard.*') ? 'text-blue-700' : 'text-slate-600 hover:text-slate-900' }}">Leaderboard</a>
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-red-700">Admin</a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="relative quiet-button" aria-label="Notifications">
                        Notifications
                        @if(auth()->user()->unreadNotifications()->count())
                            <span class="ml-2 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] text-white">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('questions.create') }}" class="primary-button">Ask a question</a>
                    <div class="relative" x-data="{ menu: false }">
                        <button @click="menu = !menu" @click.outside="menu = false" class="rounded-md px-3 py-2 text-left hover:bg-slate-100" aria-label="Open user menu">
                            <span class="block text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                            <span class="block text-xs text-slate-500">Level {{ auth()->user()->level }} · {{ auth()->user()->xp }} XP</span>
                        </button>
                        <div x-cloak x-show="menu" class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 bg-white py-2 shadow-lg">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">My activity</a>
                            <a href="{{ route('profile.show', auth()->user()) }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Public profile</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">Log out</button></form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="quiet-button">Log in</a>
                    <a href="{{ route('register') }}" class="primary-button">Join AskHub</a>
                @endauth
            </div>

            <button @click="open = !open" class="quiet-button md:hidden" aria-label="Toggle navigation">Menu</button>
        </div>
        <div x-cloak x-show="open" class="border-t border-slate-200 px-4 py-4 md:hidden">
            <div class="flex flex-col gap-3">
                <a href="{{ route('questions.index') }}">Questions</a>
                <a href="{{ route('leaderboard.index') }}">Leaderboard</a>
                @auth
                    <a href="{{ route('dashboard') }}">My activity</a>
                    <a href="{{ route('notifications.index') }}">Notifications</a>
                    <a href="{{ route('profile.show', auth()->user()) }}">Public profile</a>
                    <a href="{{ route('profile.edit') }}">Settings</a>
                    <a href="{{ route('questions.create') }}" class="primary-button justify-center">Ask a question</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left text-sm font-medium text-red-700">Log out</button></form>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))<div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8"><div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div></div>@endif
    @if(session('error'))<div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8"><div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div></div>@endif

    <main class="py-8">{{ $slot }}</main>

    <footer class="mt-12 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-2 px-4 py-6 text-sm text-slate-500 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} AskHub. Questions become shared knowledge.</p>
            <a href="{{ route('sitemap') }}" class="hover:text-slate-800">Sitemap</a>
        </div>
    </footer>
</body>
</html>
