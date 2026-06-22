<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8" x-data="questionComposer(@js(old('title', '')))">
        <div class="mb-6">
            <p class="font-mono text-xs font-semibold uppercase tracking-widest text-blue-700">New question</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Make the problem answerable</h1>
            <p class="mt-2 text-slate-600">Use a precise title, include what you tried, and tag the technologies involved.</p>
        </div>
        <form action="{{ route('questions.store') }}" method="POST" class="space-y-7 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            <div>
                <label for="title" class="mb-2 block text-sm font-semibold text-slate-800">Title</label>
                <input type="text" name="title" id="title" x-model="title" @input="lookup" value="{{ old('title') }}" maxlength="255"
                    class="w-full rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600" placeholder="How do I…?" required>
                @error('title')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                <div x-cloak x-show="suggestions.length" class="mt-3 rounded-md border border-amber-200 bg-amber-50 p-4">
                    <p class="mb-2 text-sm font-semibold text-amber-900">Similar questions may already have an answer</p>
                    <template x-for="item in suggestions" :key="item.url"><a :href="item.url" class="block py-1 text-sm text-amber-900 underline" x-text="item.title"></a></template>
                </div>
            </div>
            <x-markdown-editor name="body" label="Details" :value="old('body', '')" rows="12" />
            <div>
                <label for="tags" class="mb-2 block text-sm font-semibold text-slate-800">Tags</label>
                <input id="tags" name="tags" value="{{ old('tags') }}" class="w-full rounded-md border-slate-300 font-mono text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="laravel, mysql, authentication" required>
                <p class="mt-1 text-xs text-slate-500">Add 1–5 comma-separated tags.</p>
                @error('tags')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3"><button class="primary-button">Publish question</button><a href="{{ route('questions.index') }}" class="quiet-button">Cancel</a></div>
        </form>
    </div>
</x-app-layout>
