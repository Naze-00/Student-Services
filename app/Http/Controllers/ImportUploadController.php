<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessServiceRequestImport;
use App\Models\ImportLog;
use Illuminate\Http\Request;

class ImportUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $file     = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path     = $file->store('imports', 'public');

        $log = ImportLog::create([
            'filename' => $filename,
            'user_id'  => auth()->id(),
            'status'   => 'Processing',
        ]);

        ProcessServiceRequestImport::dispatch($path, $log->id, auth()->id());

        return response()->json([
            'success' => true,
            'log_id'  => $log->id,
            'message' => 'File uploaded. Processing in background…',
        ]);
    }
}