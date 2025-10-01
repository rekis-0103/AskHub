<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- User Info Card --}}
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $user->name }}</h1>
                    @if($user->title)
                        <div class="mt-2 inline-flex items-center px-4 py-2 rounded-lg text-white font-semibold" style="background-color: {{ $user->title->color }}">
                            {{ $user->title->name }}
                        </div>
                    @endif
                    <div class="mt-4 flex items-center space-x-4 text-gray-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                            <span>Level {{ $user->level }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>{{ $user->xp }} XP</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Member since {{ $user->created_at->format('M Y') }}
                        </div>
                    </div>
                </div>

                @auth
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                            Edit Profile
                        </a>
                    @endif
                @endauth
            </div>

            {{-- XP Progress Bar --}}
            @php
                $currentLevel = \App\Models\Level::where('level', $user->level)->first();
                $nextLevel = \App\Models\Level::where('level', $user->level + 1)->first();
                
                if ($nextLevel) {
                    $xpNeeded = $nextLevel->xp_required - $currentLevel->xp_required;
                    $xpProgress = $user->xp - $currentLevel->xp_required;
                    $percentage = ($xpProgress / $xpNeeded) * 100;
                } else {
                    $percentage = 100;
                }
            @endphp

            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progress to Level {{ $user->level + 1 }}</span>
                    <span>{{ $user->xp }} / {{ $nextLevel ? $nextLevel->xp_required : $user->xp }} XP</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-indigo-600 h-4 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-indigo-600">{{ $stats['questions_count'] }}</div>
                <div class="text-gray-600">Questions Asked</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-green-600">{{ $stats['answers_count'] }}</div>
                <div class="text-gray-600">Answers Given</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['best_answers_count'] }}</div>
                <div class="text-gray-600">Best Answers</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['total_votes'] }}</div>
                <div class="text-gray-600">Total Votes</div>
            </div>
        </div>

        {{-- Available Titles --}}
        @auth
            @if(auth()->id() === $user->id && $availableTitles->count() > 0)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Titles</h2>
                    <p class="text-gray-600 mb-4">Select a title to display on your profile</p>
                    
                    <form action="{{ route('profile.updateTitle') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ !$user->title_id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200' }}">
                                <input type="radio" name="title_id" value="" {{ !$user->title_id ? 'checked' : '' }} class="mr-2">
                                <span class="text-gray-600">No Title</span>
                            </label>
                            
                            @foreach($availableTitles as $title)
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ $user->title_id == $title->id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200' }}">
                                    <input type="radio" name="title_id" value="{{ $title->id }}" {{ $user->title_id == $title->id ? 'checked' : '' }} class="mr-2">
                                    <span class="font-semibold" style="color: {{ $title->color }}">{{ $title->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <button type="submit" class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                            Update Title
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        {{-- Recent Activity --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Activity</h2>
            
            {{-- Questions --}}
            @if($user->questions->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Recent Questions</h3>
                    <div class="space-y-3">
                        @foreach($user->questions->take(5) as $question)
                            <a href="{{ route('questions.show', $question) }}" class="block p-4 border rounded-lg hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ $question->title }}</h4>
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                            <span>{{ $question->votes }} votes</span>
                                            <span>{{ $question->answers->count() }} answers</span>
                                            <span>{{ $question->views }} views</span>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $question->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Answers --}}
            @if($user->answers->count() > 0)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Recent Answers</h3>
                    <div class="space-y-3">
                        @foreach($user->answers->take(5) as $answer)
                            <a href="{{ route('questions.show', $answer->question_id) }}" class="block p-4 border rounded-lg hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ $answer->question->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($answer->body, 150) }}</p>
                                        <div class="flex items-center space-x-4 mt-2 text-sm">
                                            <span class="text-gray-500">{{ $answer->votes }} votes</span>
                                            @if($answer->is_best)
                                                <span class="text-green-600 font-semibold">✓ Best Answer</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-400">{{ $answer->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($user->questions->count() == 0 && $user->answers->count() == 0)
                <p class="text-gray-500 text-center py-8">No activity yet</p>
            @endif
        </div>
    </div>
</x-app-layout>