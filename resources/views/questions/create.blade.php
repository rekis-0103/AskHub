<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Ask a Question</h1>
            
            <form action="{{ route('questions.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Question Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="e.g. How do I center a div in CSS?" required>
                    @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-2">Question Details</label>
                    <textarea name="body" id="body" rows="10" 
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                        placeholder="Provide more details about your question..." required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Minimum 20 characters</p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                        Post Question (+10 XP)
                    </button>
                    <a href="{{ route('questions.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>