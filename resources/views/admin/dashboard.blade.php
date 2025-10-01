<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">Pending Reports</p>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['pending_reports'] }}</p>
                    </div>
                    <svg class="w-12 h-12 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_users'] }}</p>
                    </div>
                    <svg class="w-12 h-12 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">Total Questions</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_questions'] }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600">Total Answers</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_answers'] }}</p>
                    </div>
                    <svg class="w-12 h-12 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('admin.reports.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Manage Reports</h3>
                <p class="text-gray-600">Review and moderate reported content</p>
            </a>

            <a href="{{ route('admin.badwords.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Manage Bad Words</h3>
                <p class="text-gray-600">Add or remove filtered words</p>
            </a>
        </div>

        {{-- Recent Reports --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-900">Recent Reports</h2>
            </div>
            
            <div class="divide-y">
                @forelse($recentReports as $report)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($report->status === 'reviewed' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $report->reportable_type === 'App\Models\Question' ? 'Question' : 'Answer' }}
                                    </span>
                                </div>
                                <p class="text-gray-700 mb-2">{{ $report->reason }}</p>
                                <p class="text-sm text-gray-500">
                                    Reported by <span class="font-medium">{{ $report->user->name }}</span> • {{ $report->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <a href="{{ route('admin.reports.show', $report) }}" class="ml-4 text-indigo-600 hover:text-indigo-800 font-medium">
                                Review →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        No pending reports
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>