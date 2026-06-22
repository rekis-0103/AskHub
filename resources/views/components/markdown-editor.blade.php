@props(['name' => 'body', 'label' => 'Details', 'value' => '', 'rows' => 10])

<div x-data="markdownEditor(@js($value))">
    <div class="mb-2 flex items-center justify-between">
        <label for="{{ $name }}" class="text-sm font-semibold text-slate-800">{{ $label }}</label>
        <button type="button" @click="preview" class="text-sm font-medium text-blue-700 hover:text-blue-900">
            <span x-show="!loading">Preview Markdown</span><span x-show="loading">Rendering…</span>
        </button>
    </div>
    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" x-model="body"
        class="w-full rounded-md border-slate-300 font-mono text-sm focus:border-blue-600 focus:ring-blue-600" required></textarea>
    <p class="mt-1 text-xs text-slate-500">Markdown supported. Use fenced code blocks for code and @username to mention someone.</p>
    <div x-cloak x-show="html" class="content-prose mt-4 rounded-md border border-slate-200 bg-slate-50 p-4" x-html="html"></div>
    @error($name)<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
</div>
