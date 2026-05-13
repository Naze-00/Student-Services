<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-3xl font-bold text-gray-800">{{ $stats['total_students'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Students</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-3xl font-bold text-green-600">{{ $stats['active_students'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Active Students</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_requests'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Requests</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-3xl font-bold text-yellow-500">{{ $stats['pending_requests'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Pending</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-3xl font-bold text-green-500">{{ $stats['approved_requests'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Approved</div>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <div class="text-3xl font-bold text-red-500">{{ $stats['rejected_requests'] }}</div>
            <div class="text-sm text-gray-500 mt-1">Rejected</div>
        </div>
    </div>

    {{-- Recent Requests --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-700">Recent Service Requests</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Request ID</th>
                    <th class="px-4 py-3 text-left">Student</th>
                    <th class="px-4 py-3 text-left">Service Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentRequests as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">{{ $req->request_id }}</td>
                    <td class="px-4 py-3 font-medium">{{ $req->student->full_name }}</td>
                    <td class="px-4 py-3">{{ $req->service_type }}</td>
                    <td class="px-4 py-3">
                        @php
                            $colors = ['Pending'=>'bg-yellow-100 text-yellow-700','Approved'=>'bg-green-100 text-green-700','Rejected'=>'bg-red-100 text-red-700'];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colors[$req->status] }}">
                            {{ $req->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $req->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">No requests yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t">
            <a href="{{ route('service-requests.index') }}" class="text-sm text-blue-600 hover:underline">
                View all requests →
            </a>
        </div>
    </div>
</div>