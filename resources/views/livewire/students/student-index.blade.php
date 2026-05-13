<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Students</h1>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('students.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Student
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex gap-4 mb-4">
        <input wire:model.live="search" placeholder="Search by name or student number..."
            class="border rounded px-3 py-2 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select wire:model.live="statusFilter" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Student #</th>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Grade Level</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono">{{ $student->student_number }}</td>
                    <td class="px-4 py-3 font-medium">{{ $student->full_name }}</td>
                    <td class="px-4 py-3">{{ $student->grade_level }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $student->email }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $student->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $student->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('students.edit', $student->id) }}"
                           class="text-blue-600 hover:underline text-sm">Edit</a>
                        <button wire:click="deleteStudent({{ $student->id }})"
                                wire:confirm="Delete this student?"
                                class="text-red-500 hover:underline text-sm">Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">
            {{ $students->links() }}
        </div>
    </div>
</div>