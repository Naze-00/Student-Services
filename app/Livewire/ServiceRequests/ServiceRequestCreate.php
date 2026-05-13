<?php

namespace App\Livewire\ServiceRequests;

use App\Models\ServiceRequest;
use App\Models\Student;
use Livewire\Component;

class ServiceRequestCreate extends Component
{
    public string $student_id     = '';
    public string $service_type   = '';
    public string $date_requested = '';
    public string $remarks        = '';
    public string $studentSearch  = '';

    public array $studentResults  = [];

    protected array $rules = [
        'student_id'     => 'required|exists:students,id',
        'service_type'   => 'required|in:ID Replacement,Good Moral Certificate,Form 137',
        'date_requested' => 'required|date',
        'remarks'        => 'nullable|string|max:500',
    ];

    public function updatedStudentSearch(): void
    {
        if (strlen($this->studentSearch) < 2) {
            $this->studentResults = [];
            return;
        }

        $this->studentResults = Student::where('status', 'Active')
            ->where(function ($q) {
                $q->where('first_name',      'like', "%{$this->studentSearch}%")
                  ->orWhere('last_name',     'like', "%{$this->studentSearch}%")
                  ->orWhere('student_number','like', "%{$this->studentSearch}%");
            })
            ->limit(8)
            ->get(['id', 'student_number', 'first_name', 'last_name'])
            ->toArray();
    }

    public function selectStudent(int $id, string $name): void
    {
        $this->student_id     = (string) $id;
        $this->studentSearch  = $name;
        $this->studentResults = [];
    }

    public function save()
    {
        $this->validate();

        ServiceRequest::create([
            'student_id'     => $this->student_id,
            'service_type'   => $this->service_type,
            'date_requested' => $this->date_requested,
            'remarks'        => $this->remarks ?: null,
            'status'         => 'Pending',
        ]);

        session()->flash('success', 'Service request submitted successfully.');

        return redirect()->route('service-requests.index');
    }

    public function render()
    {
        return view('livewire.service-requests.service-request-create')
            ->layout('layouts.app');
    }
}