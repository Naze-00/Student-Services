<?php

namespace App\Livewire\Students;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class StudentIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function deleteStudent(int $id): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        Student::findOrFail($id)->delete();
        session()->flash('success', 'Student deleted.');
    }

    public function render()
    {
        $students = Student::query()
            ->when($this->search, fn($q) =>
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name',  'like', "%{$this->search}%")
                  ->orWhere('student_number', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.students.student-index', compact('students'))
            ->layout('layouts.app');
    }
}