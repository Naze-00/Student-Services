{{-- resources/views/livewire/imports/import-index.blade.php --}}
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Excel Import</h1>

    {{-- ─── Upload Form ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Upload Excel File</h2>
        <p class="text-sm text-gray-500 mb-4">
            Required columns:
            <code class="bg-gray-100 px-1 rounded">student_number</code>,
            <code class="bg-gray-100 px-1 rounded">service_type</code>,
            <code class="bg-gray-100 px-1 rounded">requested_date</code>
        </p>

        <form id="upload-form">
            @csrf
            <div class="flex gap-3 items-start">
                <div class="flex-1">
                    <input
                        type="file"
                        id="import-file"
                        name="file"
                        accept=".xlsx,.xls"
                        class="w-full border rounded px-3 py-2 text-sm">

                    <p id="upload-error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <button
                    type="submit"
                    id="upload-btn"
                    class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700
                           font-medium text-sm whitespace-nowrap flex items-center gap-2
                           min-w-[150px] justify-center
                           disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="btn-label">Upload &amp; Import</span>
                    <span id="btn-spinner" class="hidden flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Uploading…
                    </span>
                </button>
            </div>
        </form>

        <p id="upload-success" class="hidden mt-3 text-sm text-green-700 bg-green-50
            border border-green-200 rounded px-3 py-2"></p>
    </div>

    {{-- ─── Import Status Indicator ────────--}}
    @if($latestLog)
    <div
        class="bg-white rounded-lg shadow mb-6 overflow-hidden border-l-4
            {{ $latestLog->status === 'Completed' ? 'border-green-500'
               : ($latestLog->status === 'Failed'   ? 'border-red-500'
               : 'border-yellow-400') }}"
        @if($latestLog->status === 'Processing') wire:poll.3000ms="$refresh" @endif
    >
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                @if($latestLog->status === 'Processing')
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow-100">
                        <svg class="animate-spin h-4 w-4 text-yellow-600"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                @elseif($latestLog->status === 'Completed')
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-4 w-4 text-green-600" fill="none"
                             stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                @else
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-4 w-4 text-red-600" fill="none"
                             stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                @endif
                <div>
                    <p class="font-semibold text-sm text-gray-800">{{ $latestLog->filename }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $latestLog->created_at->diffForHumans() }}
                        · {{ $latestLog->user->name ?? 'Unknown' }}
                    </p>
                </div>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full
                {{ $latestLog->status === 'Completed' ? 'bg-green-100 text-green-700'
                   : ($latestLog->status === 'Failed'   ? 'bg-red-100 text-red-700'
                   : 'bg-yellow-100 text-yellow-700') }}">
                {{ $latestLog->status }}
            </span>
        </div>

        @if($latestLog->status === 'Processing')
            <div class="px-5 py-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600 font-medium">Processing your file in the background…</p>
                    <p class="text-xs text-gray-400">Auto-refreshing every 3 s</p>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 w-1/2 rounded-full bg-yellow-400 animate-pulse"></div>
                </div>
            </div>

        @elseif($latestLog->status === 'Completed' && $latestLog->summary_json)
            <div class="px-5 py-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <div class="rounded-lg bg-gray-50 border border-gray-200 px-4 py-3 text-center">
                        <div class="text-2xl font-bold text-gray-800">{{ $latestLog->summary_json['total_rows'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Total rows</div>
                    </div>
                    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $latestLog->summary_json['successful_requests'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Requests created</div>
                    </div>
                    <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $latestLog->summary_json['new_students_created'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">New students</div>
                    </div>
                    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-center">
                        <div class="text-2xl font-bold text-red-500">{{ count($latestLog->summary_json['skipped_rows'] ?? []) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Skipped rows</div>
                    </div>
                </div>

                @if(!empty($latestLog->summary_json['skipped_rows']))
                <details class="group">
                    <summary class="flex items-center gap-2 text-sm text-gray-500
                                    cursor-pointer hover:text-gray-700 select-none list-none">
                        <svg class="h-3.5 w-3.5 transition-transform group-open:rotate-90"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 6l8 4-8 4V6z"/>
                        </svg>
                        View {{ count($latestLog->summary_json['skipped_rows']) }} skipped row(s)
                    </summary>
                    <ul class="mt-2 text-xs text-red-700 bg-red-50 border border-red-200
                               rounded-lg p-3 space-y-1 max-h-48 overflow-y-auto">
                        @foreach($latestLog->summary_json['skipped_rows'] as $skip)
                            <li class="flex gap-2">
                                <span class="font-medium shrink-0">Row {{ $skip['row'] }}:</span>
                                <span>{{ $skip['reason'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </details>
                @else
                    <p class="text-xs text-green-600">✓ All rows processed successfully.</p>
                @endif
            </div>

        @elseif($latestLog->status === 'Failed')
            <div class="px-5 py-4">
                <p class="text-sm text-red-600 font-medium mb-1">Import failed.</p>
                @if(!empty($latestLog->summary_json['error']))
                    <p class="text-xs text-red-500 bg-red-50 border border-red-200 rounded p-2 font-mono break-all">
                        {{ $latestLog->summary_json['error'] }}
                    </p>
                @else
                    <p class="text-xs text-gray-500">No additional error details were recorded.</p>
                @endif
            </div>
        @endif
    </div>
    @endif

    {{-- ─── Past Import Logs ────────── --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b">
            <h2 class="font-semibold text-gray-700">Past Imports</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Filename</th>
                    <th class="px-4 py-3 text-left">Uploaded by</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Rows</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $log->filename }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $log->user->name }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium
                            {{ $log->status === 'Completed' ? 'bg-green-100 text-green-700'
                               : ($log->status === 'Failed'   ? 'bg-red-100 text-red-700'
                               : 'bg-yellow-100 text-yellow-700') }}">
                            @if($log->status === 'Processing')
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            @elseif($log->status === 'Completed')
                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                     stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                     stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $log->summary_json['total_rows'] ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($log->summary_json)
                        <button
                            wire:click="$set('latestLogId', {{ $log->id }})"
                            class="text-blue-600 hover:underline text-xs">
                            View summary
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">No imports yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $logs->links() }}</div>
    </div>
</div>

<script>
document.getElementById('upload-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const fileInput   = document.getElementById('import-file');
    const btn         = document.getElementById('upload-btn');
    const btnLabel    = document.getElementById('btn-label');
    const btnSpinner  = document.getElementById('btn-spinner');
    const errorBox    = document.getElementById('upload-error');
    const successBox  = document.getElementById('upload-success');

    errorBox.classList.add('hidden');
    errorBox.textContent = '';
    successBox.classList.add('hidden');

    if (!fileInput.files.length) {
        errorBox.textContent = 'Please select a file first.';
        errorBox.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    btnLabel.classList.add('hidden');
    btnSpinner.classList.remove('hidden');

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    try {
        const response = await fetch('{{ route("imports.upload") }}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        });

        const data = await response.json();

        if (response.ok && data.success) {
            successBox.textContent = data.message;
            successBox.classList.remove('hidden');
            fileInput.value = '';

            @this.set('latestLogId', data.log_id).then(() => {
                @this.call('refreshLog');
            });
        } else {
            const msg = data.errors?.file?.[0]
                ?? data.message
                ?? 'Upload failed. Please try again.';
            errorBox.textContent = msg;
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        errorBox.textContent = 'Network error — please try again.';
        errorBox.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btnLabel.classList.remove('hidden');
        btnSpinner.classList.add('hidden');
    }
});
</script>