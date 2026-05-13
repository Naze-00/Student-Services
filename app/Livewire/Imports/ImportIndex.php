<?php

namespace App\Livewire\Imports;

use App\Jobs\ProcessServiceRequestImport;
use App\Models\ImportLog;
use Livewire\Component;
use Livewire\WithPagination;

class ImportIndex extends Component
{

    use WithPagination;

    public ?int $latestLogId = null;
    public string $uploadError = '';

    public function refreshLog(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = ImportLog::with('user')->latest()->paginate(10);

        $latestLog = $this->latestLogId
            ? ImportLog::with('user')->find($this->latestLogId)
            : ImportLog::with('user')
                ->where('user_id', auth()->id())
                ->latest()
                ->first();

        return view('livewire.imports.import-index', compact('logs', 'latestLog'))
            ->layout('layouts.app');
    }
}