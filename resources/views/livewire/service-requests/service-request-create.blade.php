<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('service-requests.index') }}" class="text-gray-500 hover:text-gray-700">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800">New Service Request</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="save" class="space-y-5">

            {{-- Student Search --}}
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Student <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model.live="studentSearch"
                    type="text"
                    placeholder="Search by name or student number..."
                    autocomplete="off"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                           @error('student_id') border-red-500 @enderror">

                {{-- Dropdown Results --}}
                @if(count($studentResults) > 0)
                <div class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-56 overflow-y-auto">
                    @foreach($studentResults as $student)
                    <button
                        type="button"
                        wire:click="selectStudent({{ $student['id'] }}, '{{ addslashes($student['first_name'] . ' ' . $student['last_name']) }}')"
                        class="w-full text-left px-4 py-2.5 hover:bg-blue-50 border-b border-gray-100 last:border-0">
                        <span class="font-medium text-gray-800">{{ $student['first_name'] }} {{ $student['last_name'] }}</span>
                        <span class="text-gray-400 text-xs ml-2">{{ $student['student_number'] }}</span>
                    </button>
                    @endforeach
                </div>
                @endif

                @if($student_id)
                <p class="text-green-600 text-xs mt-1">✓ Student selected (ID: {{ $student_id }})</p>
                @endif

                @error('student_id')
                <span class="text-red-500 text-xs">Please select a valid active student.</span>
                @enderror
            </div>

            {{-- Service Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Service Type <span class="text-red-500">*</span>
                </label>
                <select wire:model="service_type"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                           @error('service_type') border-red-500 @enderror">
                    <option value="">-- Select a service --</option>
                    <option value="ID Replacement">ID Replacement</option>
                    <option value="Good Moral Certificate">Good Moral Certificate</option>
                    <option value="Form 137">Form 137</option>
                </select>
                @error('service_type')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Date Requested --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date Requested <span class="text-red-500">*</span>
                </label>
                <input wire:model="date_requested" type="date"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                           @error('date_requested') border-red-500 @enderror">
                @error('date_requested')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remarks --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea wire:model="remarks" rows="3"
                    placeholder="Any additional notes..."
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                @error('remarks')
                <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-medium">
                    Submit Request
                </button>
                <a href="{{ route('service-requests.index') }}"
                   class="px-6 py-2 border rounded text-gray-600 hover:bg-gray-50">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>