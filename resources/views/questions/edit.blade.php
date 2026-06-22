<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6"><p class="font-mono text-xs font-semibold uppercase tracking-widest text-blue-700">Revision</p><h1 class="mt-1 text-3xl font-bold text-slate-950">Edit question</h1></div>
        <form action="{{ route('questions.update', $question) }}" method="POST" class="space-y-7 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')
            <div><label for="title" class="mb-2 block text-sm font-semibold text-slate-800">Title</label><input id="title" name="title" value="{{ old('title', $question->title) }}" class="w-full rounded-md border-slate-300 focus:border-blue-600 focus:ring-blue-600" required>@error('title')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror</div>
            <x-markdown-editor name="body" label="Details" :value="old('body', $question->body)" rows="12" />
            <div><label for="tags" class="mb-2 block text-sm font-semibold text-slate-800">Tags</label><input id="tags" name="tags" value="{{ old('tags', $question->tags->pluck('name')->implode(', ')) }}" class="w-full rounded-md border-slate-300 font-mono text-sm focus:border-blue-600 focus:ring-blue-600" required><p class="mt-1 text-xs text-slate-500">Add 1–5 comma-separated tags.</p></div>
            <div class="flex gap-3"><button class="primary-button">Save changes</button><a href="{{ $question->public_url }}" class="quiet-button">Cancel</a></div>
        </form>
    </div>
</x-app-layout>
