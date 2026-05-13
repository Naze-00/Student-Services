<?php

namespace App\Livewire\Students;

use App\Models\Student;
use Livewire\Component;

class StudentEdit extends Component
{
    public int    $studentId;
    public string $student_number = '';
    public string $first_name     = '';
    public string $last_name      = '';
    public string $grade_level    = '';
    public string $email          = '';
    public string $status         = 'Active';

    public function mount(int $id): void
    {
        $student = Student::findOrFail($id);
        $this->studentId     = $student->id;
        $this->student_number = $student->student_number;
        $this->first_name    = $student->first_name;
        $this->last_name     = $student->last_name;
        $this->grade_level   = $student->grade_level;
        $this->email         = $student->email;
        $this->status        = $student->status;
    }

    protected function rules(): array
    {
        return [
            'student_number' => "required|unique:students,student_number,{$this->studentId}|max:50",
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'grade_level'    => 'required|string|max:50',
            'email'          => "required|email|unique:students,email,{$this->studentId}|max:255",
            'status'         => 'required|in:Active,Inactive',
        ];
    }

    public function update()
    {
        $this->validate();

        Student::findOrFail($this->studentId)->update([
            'student_number' => $this->student_number,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'grade_level'    => $this->grade_level,
            'email'          => $this->email,
            'status'         => $this->status,
        ]);

        session()->flash('success', 'Student updated.');
        return redirect()->route('students.index');
    }

    public function render()
    {
        return view('livewire.students.student-edit')->layout('layouts.app');
    }
}