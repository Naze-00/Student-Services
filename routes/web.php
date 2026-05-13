<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Students\StudentIndex;
use App\Livewire\Students\StudentCreate;
use App\Livewire\Students\StudentEdit;
use App\Livewire\ServiceRequests\ServiceRequestIndex;
use App\Livewire\ServiceRequests\ServiceRequestShow;
use App\Livewire\Imports\ImportIndex;
use App\Livewire\ServiceRequests\ServiceRequestCreate;
use App\Http\Controllers\ImportUploadController;

Route::get('/', fn() => redirect('/login'));
Route::get('/login',  Login::class)->name('login')->middleware('guest');
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Students
    Route::get('/students',        StudentIndex::class)->name('students.index');
    Route::get('/students/create', StudentCreate::class)->name('students.create')->middleware('admin');
    Route::get('/students/{id}/edit', StudentEdit::class)->name('students.edit')->middleware('admin');

    // Service Requests
    Route::get('/service-requests',        ServiceRequestIndex::class)->name('service-requests.index');
    Route::get('/service-requests/create', ServiceRequestCreate::class)->name('service-requests.create');
    Route::get('/service-requests/{id}',   ServiceRequestShow::class)->name('service-requests.show');

    // Imports (admin only)
    Route::get('/imports', ImportIndex::class)->name('imports.index')->middleware('admin');
    Route::post('/imports/upload', [ImportUploadController::class, 'store'])->name('imports.upload')->middleware(['auth']);

});