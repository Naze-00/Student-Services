<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('service-requests.index') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">Service Request Detail</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Request ID:</span> <span class="font-mono font-medium">{{ $serviceRequest->request_id }}</span></div>
            <div><span class="text-gray-500">Status:</span>
                @php $colors = ['Pending'=>'bg-yellow-100 text-yellow-700','Approved'=>'bg-green-100 text-green-700','Rejected'=>'bg-red-100 text-red-700']; @endphp
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$serviceRequest->status] }}">
                    {{ $serviceRequest->status }}
                </span>
            </div>
            <div><span class="text-gray-500">Student:</span> <span class="font-medium">{{ $serviceRequest->student->full_name }}</span></div>
            <div><span class="text-gray-500">Student #:</span> {{ $serviceRequest->student->student_number }}</div>
            <div><span class="text-gray-500">Service Type:</span> {{ $serviceRequest->service_type }}</div>
            <div><span class="text-gray-500">Date Requested:</span> {{ $serviceRequest->date_requested }}</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
            <textarea wire:model="remarks" rows="3"
                class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                placeholder="Optional remarks..."></textarea>
        </div>

        @if($serviceRequest->status === 'Pending')
        <div class="flex gap-3">
            <button wire:click="approve"
                    class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 font-medium">
                Approve
            </button>
            <button wire:click="reject"
                    class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 font-medium">
                Reject
            </button>
        </div>
        @endif
    </div>
</div>