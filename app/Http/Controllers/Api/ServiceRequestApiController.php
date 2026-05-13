<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRequestApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = ServiceRequest::with('student')
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('date_requested', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('date_requested', '<=', $request->date_to))
            ->paginate(15);

        return response()->json($requests);
    }

    public function approve(int $id, Request $request): JsonResponse
    {
        $req = ServiceRequest::findOrFail($id);
        $req->update(['status' => 'Approved', 'remarks' => $request->remarks]);
        return response()->json(['message' => 'Request approved.', 'data' => $req]);
    }

    public function reject(int $id, Request $request): JsonResponse
    {
        $req = ServiceRequest::findOrFail($id);
        $req->update(['status' => 'Rejected', 'remarks' => $request->remarks]);
        return response()->json(['message' => 'Request rejected.', 'data' => $req]);
    }
}