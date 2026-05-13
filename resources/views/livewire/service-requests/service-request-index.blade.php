<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Service Requests</h1>
        {{-- Both admin and staff can create requests --}}
        <a href="{{ route('service-requests.create') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium text-sm">
            + New Request
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3">
        <input wire:model.live="search" placeholder="Search student..."
            class="border rounded px-3 py-2 flex-1 min-w-48 focus:ring-2 focus:ring-blue-500">
        <select wire:model.live="statusFilter" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
        <input wire:model.live="dateFrom" type="date" class="border rounded px-3 py-2">
        <input wire:model.live="dateTo"   type="date" class="border rounded px-3 py-2">
    </div>
    

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Request ID</th>
                    <th class="px-4 py-3 text-left">Student</th>
                    <th class="px-4 py-3 text-left">Service Type</th>
                    <th class="px-4 py-3 text-left">Date Requested</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($requests as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">{{ $req->request_id }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $req->student->full_name }}</div>
                        <div class="text-gray-400 text-xs">{{ $req->student->student_number }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $req->service_type }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $req->date_requested }}</td>
                    <td class="px-4 py-3">
                        @php
                            $colors = ['Pending'=>'bg-yellow-100 text-yellow-700','Approved'=>'bg-green-100 text-green-700','Rejected'=>'bg-red-100 text-red-700'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$req->status] }}">
                            {{ $req->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-2 items-center">
                        <a href="{{ route('service-requests.show', $req->id) }}" class="text-blue-600 hover:underline">View</a>
                        @if(auth()->user()->isAdmin())
                        <button wire:click="delete({{ $req->id }})"
                                wire:confirm="Delete this request?"
                                class="text-red-500 hover:underline">Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $requests->links() }}
        </div>
    </div>
</div>