<?php

namespace App\Livewire\ServiceRequests;

use App\Models\ServiceRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceRequestIndex extends Component
{
    use WithPagination;

    public string $statusFilter    = '';
    public string $dateFrom        = '';
    public string $dateTo          = '';
    public string $search          = '';

    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void     { $this->resetPage(); }
    public function updatingDateTo(): void       { $this->resetPage(); }

    public function delete(int $id): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        ServiceRequest::findOrFail($id)->delete();
        session()->flash('success', 'Request deleted.');
    }

    public function render()
    {
        $requests = ServiceRequest::with('student')
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom,     fn($q) => $q->whereDate('date_requested', '>=', $this->dateFrom))
            ->when($this->dateTo,       fn($q) => $q->whereDate('date_requested', '<=', $this->dateTo))
            ->when($this->search, fn($q) =>
                $q->whereHas('student', fn($sq) =>
                    $sq->where('first_name',     'like', "%{$this->search}%")
                       ->orWhere('last_name',    'like', "%{$this->search}%")
                       ->orWhere('student_number','like', "%{$this->search}%")
                )
            )
            ->latest()
            ->paginate(15);

        return view('livewire.service-requests.service-request-index', compact('requests'))
            ->layout('layouts.app');
    }
}