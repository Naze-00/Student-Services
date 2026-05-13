<?php

namespace App\Livewire\Students;

use App\Models\Student;
use Livewire\Component;

class StudentCreate extends Component
{
    public string $student_number = '';
    public string $first_name     = '';
    public string $last_name      = '';
    public string $grade_level    = '';
    public string $email          = '';
    public string $status         = 'Active';

    protected array $rules = [
        'student_number' => 'required|unique:students,student_number|max:50',
        'first_name'     => 'required|string|max:100',
        'last_name'      => 'required|string|max:100',
        'grade_level'    => 'required|string|max:50',
        'email'          => 'required|email|unique:students,email|max:255',
        'status'         => 'required|in:Active,Inactive',
    ];

    public function save()
    {
        $this->validate();

        Student::create([
            'student_number' => $this->student_number,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'grade_level'    => $this->grade_level,
            'email'          => $this->email,
            'status'         => $this->status,
        ]);

        session()->flash('success', 'Student created successfully.');
        return redirect()->route('students.index');
    }

    public function render()
    {
        return view('livewire.students.student-create')->layout('layouts.app');
    }
}