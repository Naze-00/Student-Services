<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\ServiceRequest;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_students'        => Student::count(),
            'active_students'       => Student::where('status', 'Active')->count(),
            'total_requests'        => ServiceRequest::count(),
            'pending_requests'      => ServiceRequest::where('status', 'Pending')->count(),
            'approved_requests'     => ServiceRequest::where('status', 'Approved')->count(),
            'rejected_requests'     => ServiceRequest::where('status', 'Rejected')->count(),
        ];

        $recentRequests = ServiceRequest::with('student')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard', compact('stats', 'recentRequests'))
            ->layout('layouts.app');
    }
}