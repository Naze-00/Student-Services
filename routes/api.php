<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\ServiceRequestApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students',                           [StudentApiController::class, 'index']);
    Route::get('/service-requests',                  [ServiceRequestApiController::class, 'index']);
    Route::patch('/service-requests/{id}/approve',   [ServiceRequestApiController::class, 'approve']);
    Route::patch('/service-requests/{id}/reject',    [ServiceRequestApiController::class, 'reject']);
});