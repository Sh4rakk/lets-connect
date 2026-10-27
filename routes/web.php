<?php
use App\Http\Controllers\WorkshopDashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassDashboardController;
use App\Http\Controllers\Auth\LoginCodeController;
use App\Models\Workshop;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Setting::signupsOpen()
        ? redirect()->route('register')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    $bookings = $user->bookings()->with('workshopMoments.workshop', 'workshopMoments.moment')->get();
    return view('dashboard', [
        "workshops" => Workshop::all(),
        "bookings" => $bookings,
        "isOpen" => null
    ]);
})->middleware(['auth', 'verified', 'checkSignupsOpen'])->name('dashboard');

// Het opslaan van de workshopkeuze en de capaciteit gaan via de api, zie routes/api.php

// 2FA / OTP (allow both guests and already-authenticated users)
Route::get('/auth/verify-otp', [LoginCodeController::class, 'showVerifyForm'])->name('auth.verify-otp');
Route::post('/auth/login-code/request', [LoginCodeController::class, 'requestCode'])->name('auth.login-code.request');
Route::post('/auth/login-code/verify', [LoginCodeController::class, 'verify'])->name('auth.login-code.verify');


Route::view('/success', 'success');

// de cijfers haalt de pagina zelf op via /api/statistics
Route::view('/admin-dashboard', 'dashboard.selection')->middleware(['role:admin'])->name('selectionDashboard');

// inschrijvingen open/dicht zetten gaat via de api (POST /api/settings/toggle-signups)

Route::get('/students-overview', [UserController::class, 'index'])->middleware(['role:admin'])->name('students-overview');
Route::get('/edit-student/{id}', [UserController::class, 'edit'])->middleware(['role:admin'])->name('edit-student');


Route::get('/workshop-dashboard', [WorkshopDashboardController::class, 'index'])->middleware(['role:admin'])->name('workshop-dashboard');
Route::get('/workshop-moment/{wsm}', [WorkshopDashboardController::class, 'showbookings'])->middleware(['role:admin'])->name('workshop-moment.showbookings');
Route::get('/workshop-moment/{wsm}/{class}', [WorkshopDashboardController::class, 'showfilteredbookings'])->middleware(['role:admin'])->name('workshop-moment.showfilteredbookings');

Route::get('/class-dashboard', [ClassDashboardController::class, 'index'])->middleware(['role:admin'])->name('class-dashboard');

Route::get('/export/all-users', [ExportController::class, 'exportAll'])->middleware(['role:admin'])->name('export-all-users');
Route::get('/export/classes/{class}', [ExportController::class, 'exportClass'])->middleware(['role:admin'])->name('export-class');
Route::get('/export/all-classes', [ExportController::class, 'exportAllClasses'])->middleware(['role:admin'])->name('export-all-classes');
Route::get('/export/{workshopName}', [ExportController::class, 'exportWorkshop'])->middleware(['role:admin'])->name('export-workshop');

Route::get('/files/download/{file}', [FileController::class, 'download'])->middleware(['role:admin'])->name('download-export')->where('file', '[^/]+');
Route::delete('/files/delete/{file}', [FileController::class, 'delete'])->middleware(['role:admin'])->name('delete-export')->where('file', '[^/]+');

require __DIR__.'/auth.php';
