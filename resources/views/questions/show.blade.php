<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Question --}}
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex space-x-6">
                {{-- Vote Section --}}
                <div class="flex flex-col items-center space-y-2">
                    @auth
                        <form action="{{ route('questions.vote', $question) }}" method="POST">
                            @csrf
                            <input type="hidden" name="vote" value="1">
                            <button type="submit" class="text-gray-400 hover:text-green-600 {{ auth()->user()->hasVoted($question)?->vote == 1 ? 'text-green-600' : '' }}">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/>
                        </svg>
                    @endauth

                    <div class="text-3xl font-bold {{ $question->votes > 0 ? 'text-green-600' : ($question->votes < 0 ? 'text-red-600' : 'text-gray-700') }}">
                        {{ $question->votes }}
                    </div>

                    @auth
                        <form action="{{ route('questions.vote', $question) }}" method="POST">
                            @csrf
                            <input type="hidden" name="vote" value="-1">
                            <button type="submit" class="text-gray-400 hover:text-red-600 {{ auth()->user()->hasVoted($question)?->vote == -1 ? 'text-red-600' : '' }}">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/>
                                </svg>
                            </button>
                        </form>
                    @else
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/>
                        </svg>
                    @endauth
                </div>

                {{-- Question Content --}}
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $question->title }}</h1>
                    
                    <div class="prose max-w-none mb-6">
                        {!! nl2br(e($question->body)) !!}
                    </div>

                    <div class="flex items-center justify-between border-t pt-4">
                        <div class="flex space-x-4">
                            @can('update', $question)
                                <a href="{{ route('questions.edit', $question) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Edit
                                </a>
                            @endcan
                            
                            @can('delete', $question)
                                <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            @endcan
                            
                            @auth
                                <button onclick="document.getElementById('report-question-modal').classList.remove('hidden')" class="text-sm text-gray-600 hover:text-gray-800">
                                    Report
                                </button>
                            @endauth
                        </div>

                        <div class="flex items-center space-x-3">
                            <a href="{{ route('profile.show', $question->user) }}" class="flex items-center space-x-2">
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $question->user->name }}</div>
                                    @if($question->user->title)
                                        <div class="text-xs" style="color: {{ $question->user->title->color }}">
                                            {{ $question->user->title->name }}
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-500">Level {{ $question->user->level }}</div>
                                </div>
                            </a>
                            <div class="text-sm text-gray-500">
                                {{ $question->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Answers --}}
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                {{ $question->answers->count() }} {{ Str::plural('Answer', $question->answers->count()) }}
            </h2>

            @foreach($question->answers as $answer)
                <div class="bg-white rounded-lg shadow p-6 mb-4 {{ $answer->is_best ? 'border-2 border-green-500' : '' }}">
                    @if($answer->is_best)
                        <div class="mb-4 flex items-center space-x-2 text-green-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-semibold">Best Answer</span>
                        </div>
                    @endif

                    <div class="flex space-x-6">
                        {{-- Vote Section --}}
                        <div class="flex flex-col items-center space-y-2">
                            @auth
                                <form action="{{ route('answers.vote', $answer) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vote" value="1">
                                    <button type="submit" class="text-gray-400 hover:text-green-600 {{ auth()->user()->hasVoted($answer)?->vote == 1 ? 'text-green-600' : '' }}">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"/>
                                </svg>
                            @endauth

                            <div class="text-2xl font-bold {{ $answer->votes > 0 ? 'text-green-600' : ($answer->votes < 0 ? 'text-red-600' : 'text-gray-700') }}">
                                {{ $answer->votes }}
                            </div>

                            @auth
                                <form action="{{ route('answers.vote', $answer) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vote" value="-1">
                                    <button type="submit" class="text-gray-400 hover:text-red-600 {{ auth()->user()->hasVoted($answer)?->vote == -1 ? 'text-red-600' : '' }}">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"/>
                                </svg>
                            @endauth

                            @auth
                                @if(auth()->user()->canSelectBestAnswer($question) && !$answer->is_best)
                                    <form action="{{ route('answers.markAsBest', $answer) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-yellow-500" title="Mark as best answer">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>

                        {{-- Answer Content --}}
                        <div class="flex-1">
                            <div class="prose max-w-none mb-4">
                                {!! nl2br(e($answer->body)) !!}
                            </div>

                            <div class="flex items-center justify-between border-t pt-4">
                                <div class="flex space-x-4">
                                    @can('update', $answer)
                                        <a href="{{ route('answers.edit', $answer) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                            Edit
                                        </a>
                                    @endcan
                                    
                                    @can('delete', $answer)
                                        <form action="{{ route('answers.destroy', $answer) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    @endcan
                                    
                                    @auth
                                        <button onclick="document.getElementById('report-answer-{{ $answer->id }}').classList.remove('hidden')" class="text-sm text-gray-600 hover:text-gray-800">
                                            Report
                                        </button>
                                    @endauth
                                </div>

                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('profile.show', $answer->user) }}" class="flex items-center space-x-2">
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ $answer->user->name }}</div>
                                            @if($answer->user->title)
                                                <div class="text-xs" style="color: {{ $answer->user->title->color }}">
                                                    {{ $answer->user->title->name }}
                                                </div>
                                            @endif
                                            <div class="text-xs text-gray-500">Level {{ $answer->user->level }}</div>
                                        </div>
                                    </a>
                                    <div class="text-sm text-gray-500">
                                        {{ $answer->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Comments --}}
                            @if($answer->comments->count() > 0)
                                <div class="mt-4 space-y-3 border-l-2 border-gray-200 pl-4">
                                    @foreach($answer->comments as $comment)
                                        <div class="bg-gray-50 rounded p-3">
                                            <p class="text-sm text-gray-700">{{ $comment->body }}</p>
                                            <div class="flex items-center justify-between mt-2">
                                                <a href="{{ route('profile.show', $comment->user) }}" class="text-xs text-gray-600 hover:text-gray-800">
                                                    {{ $comment->user->name }} 
                                                    @if($comment->user->title)
                                                        <span style="color: {{ $comment->user->title->color }}">{{ $comment->user->title->name }}</span>
                                                    @endif
                                                </a>
                                                <div class="flex space-x-2">
                                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                    @can('delete', $comment)
                                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete comment?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Add Comment Form --}}
                            @auth
                                <form action="{{ route('comments.store', $answer) }}" method="POST" class="mt-4">
                                    @csrf
                                    <div class="flex space-x-2">
                                        <input type="text" name="body" placeholder="Add a comment..." 
                                            class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                        <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm">
                                            Comment
                                        </button>
                                    </div>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>

                {{-- Report Answer Modal --}}
                @auth
                <div id="report-answer-{{ $answer->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full">
                        <h3 class="text-lg font-semibold mb-4">Report Answer</h3>
                        <form action="{{ route('answers.report', $answer) }}" method="POST">
                            @csrf
                            <textarea name="reason" rows="4" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Why are you reporting this answer?" required></textarea>
                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="button" onclick="document.getElementById('report-answer-{{ $answer->id }}').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                    Submit Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endauth
            @endforeach
        </div>

        {{-- Answer Form --}}
        @auth
            <div class="bg-white rounded-lg shadow p-6 mt-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Your Answer</h3>
                <form action="{{ route('answers.store', $question) }}" method="POST">
                    @csrf
                    <textarea name="body" rows="6" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Write your answer here..." required></textarea>
                    @error('body')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <div class="mt-4">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                            Post Answer (+20 XP)
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-gray-100 rounded-lg p-6 mt-8 text-center">
                <p class="text-gray-600">
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Login</a> 
                    or 
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Register</a> 
                    to answer this question
                </p>
            </div>
        @endauth

        {{-- Report Question Modal --}}
        @auth
        <div id="report-question-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Report Question</h3>
                <form action="{{ route('questions.report', $question) }}" method="POST">
                    @csrf
                    <textarea name="reason" rows="4" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Why are you reporting this question?" required></textarea>
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="document.getElementById('report-question-modal').classList.add('hidden')" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endauth
    </div>
</x-app-layout>
