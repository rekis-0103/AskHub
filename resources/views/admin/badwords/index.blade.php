<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Bad words filter</h1>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Add one word</h2>
                <form action="{{ route('admin.badwords.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="word" class="block text-sm font-medium text-gray-700">Word</label>
                        <input type="text" name="word" id="word" value="{{ old('word') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            maxlength="50" required>
                        @error('word')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Add word
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bulk add (comma-separated)</h2>
                <form action="{{ route('admin.badwords.bulkStore') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="words" class="block text-sm font-medium text-gray-700">Words</label>
                        <textarea name="words" id="words" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="spam, scam, ..." required>{{ old('words') }}</textarea>
                        @error('words')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Add all
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Word</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Added</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($badWords as $badWord)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $badWord->word }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $badWord->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <form action="{{ route('admin.badwords.destroy', $badWord) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Remove this word from the filter list?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                No bad words yet. Add words above to filter them in posts.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $badWords->links() }}
        </div>
    </div>
</x-app-layout>
