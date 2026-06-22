<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <h1 class="mb-6 text-3xl font-bold text-slate-950">Edit answer</h1>
        <form action="{{ route('answers.update', $answer) }}" method="POST" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf @method('PUT')
            <x-markdown-editor name="body" label="Answer" :value="old('body', $answer->body)" rows="12" />
            <div class="flex gap-3"><button class="primary-button">Save answer</button><a href="{{ $answer->question->public_url }}" class="quiet-button">Cancel</a></div>
        </form>
    </div>
</x-app-layout>
