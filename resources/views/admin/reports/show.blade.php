<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Report detail</h1>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; Back to all reports
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Report info</h2>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $report->status === 'pending'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : ($report->status === 'reviewed'
                                        ? 'bg-blue-100 text-blue-800'
                                        : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-gray-500">Type</dt>
                        <dd class="text-gray-900">
                            {{ $report->reportable_type === 'App\\Models\\Question' ? 'Question' : 'Answer' }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-gray-500">Reported by</dt>
                        <dd class="text-gray-900">
                            {{ $report->user->name }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-gray-500">Reported at</dt>
                        <dd class="text-gray-900">
                            {{ $report->created_at->format('M d, Y H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500 mb-1">Reason</dt>
                        <dd class="text-gray-900 whitespace-pre-line">
                            {{ $report->reason }}
                        </dd>
                    </div>
                </dl>

                <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="mt-6 space-y-3">
                    @csrf
                    @method('PATCH')

                    <label class="block text-sm font-medium text-gray-700">Update status</label>
                    <select name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pending" @selected($report->status === 'pending')>Pending</option>
                        <option value="reviewed" @selected($report->status === 'reviewed')>Reviewed</option>
                        <option value="resolved" @selected($report->status === 'resolved')>Resolved</option>
                    </select>

                    @error('status')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Save status
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Reported content</h2>

                @if ($report->reportable)
                    @if ($report->reportable_type === 'App\\Models\\Question')
                        <p class="text-sm text-gray-500 mb-2">Question title</p>
                        <p class="text-base font-semibold text-gray-900 mb-4">
                            {{ $report->reportable->title }}
                        </p>

                        <p class="text-sm text-gray-500 mb-2">Question body</p>
                        <p class="text-sm text-gray-900 whitespace-pre-line mb-4">
                            {{ $report->reportable->body }}
                        </p>

                        <a href="{{ route('questions.show', $report->reportable_id) }}"
                            class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-4">
                            View on site &rarr;
                        </a>
                    @else
                        <p class="text-sm text-gray-500 mb-2">Answer body</p>
                        <p class="text-sm text-gray-900 whitespace-pre-line mb-4">
                            {{ $report->reportable->body }}
                        </p>

                        @if ($report->reportable->question_id ?? null)
                            <a href="{{ route('questions.show', $report->reportable->question_id) }}"
                                class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 mb-4">
                                View question &rarr;
                            </a>
                        @endif
                    @endif

                    <form action="{{ route('admin.reports.deleteContent', $report) }}" method="POST"
                        class="mt-4"
                        onsubmit="return confirm('Delete this content and mark report as resolved?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                            Delete content & resolve
                        </button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">
                        The reported content has already been deleted.
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

