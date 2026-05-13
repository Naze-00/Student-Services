<?php

namespace App\Livewire\ServiceRequests;

use App\Models\ServiceRequest;
use Livewire\Component;

class ServiceRequestShow extends Component
{
    public ServiceRequest $serviceRequest;
    public string $remarks = '';

    public function mount(int $id): void
    {
        $this->serviceRequest = ServiceRequest::with('student')->findOrFail($id);
        $this->remarks        = $this->serviceRequest->remarks ?? '';
    }

    public function approve(): void
    {
        $this->serviceRequest->update(['status' => 'Approved', 'remarks' => $this->remarks]);
        session()->flash('success', 'Request approved.');
    }

    public function reject(): void
    {
        $this->serviceRequest->update(['status' => 'Rejected', 'remarks' => $this->remarks]);
        session()->flash('success', 'Request rejected.');
    }

    public function render()
    {
        return view('livewire.service-requests.service-request-show')
            ->layout('layouts.app');
    }
}