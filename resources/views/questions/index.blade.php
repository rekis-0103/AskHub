<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">All Questions</h1>
            @auth
                <a href="{{ route('questions.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Ask Question
                </a>
            @endauth
        </div>

        <div class="space-y-4">
            @forelse($questions as $question)
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
                    <div class="flex space-x-4">
                        <div class="flex flex-col items-center space-y-2 text-gray-500">
                            <div class="text-center">
                                <div class="text-2xl font-semibold">{{ $question->votes }}</div>
                                <div class="text-xs">votes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-semibold {{ $question->best_answer_id ? 'text-green-600' : '' }}">
                                    {{ $question->answers_count }}
                                </div>
                                <div class="text-xs">answers</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg">{{ $question->views }}</div>
                                <div class="text-xs">views</div>
                            </div>
                        </div>

                        <div class="flex-1">
                            <a href="{{ route('questions.show', $question) }}" class="text-xl font-semibold text-indigo-600 hover:text-indigo-800">
                                {{ $question->title }}
                            </a>
                            <p class="text-gray-600 mt-2 line-clamp-2">
                                {{ Str::limit(strip_tags($question->body), 200) }}
                            </p>
                            
                            <div class="flex items-center justify-between mt-4">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">asked by</span>
                                    <a href="{{ route('profile.show', $question->user) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                        {{ $question->user->name }}
                                    </a>
                                    @if($question->user->title)
                                        <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $question->user->title->color }}20; color: {{ $question->user->title->color }};">
                                            {{ $question->user->title->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">Level {{ $question->user->level }}</span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $question->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500 text-lg">No questions yet. Be the first to ask!</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $questions->links() }}
        </div>
    </div>
</x-app-layout>