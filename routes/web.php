<?php
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\WorkshopDashboardController;
use App\Http\Controllers\WorkshopLeaderController;
use App\Http\Controllers\WorkshopQrController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackTestController;
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
Route::middleware('auth')->group(function () {
    Route::get('/workshopleider', [WorkshopLeaderController::class, 'index'])->name('leader.dashboard');
    Route::get('/workshopleider/verwachting/{wsm}', [WorkshopLeaderController::class, 'expected'])->name('leader.expected');
    Route::get('/workshopleider/feedback/{wsm}', [WorkshopLeaderController::class, 'feedback'])->name('leader.feedback');
    Route::get('/workshop-moment/{wsm}/qr', [WorkshopQrController::class, 'showRound'])->name('workshop-moment.qr');
    Route::post('/workshop-moment/{wsm}/qr/regenerate', [WorkshopQrController::class, 'regenerateRound'])->name('workshop-moment.qr.regenerate');
});

Route::get('/workshop-moment/{wsm}/{class}', [WorkshopDashboardController::class, 'showfilteredbookings'])->middleware(['role:admin'])->name('workshop-moment.showfilteredbookings');

Route::middleware('role:admin')->group(function () {
    Route::get('/workshops/create', [WorkshopController::class, 'create'])->name('workshops.create');
    Route::post('/workshops', [WorkshopController::class, 'store'])->name('workshops.store');
    Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])->name('workshops.edit');
    Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
    Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('workshops.destroy');
    Route::get('/feedback-test', [FeedbackTestController::class, 'index'])->name('feedback.test');
    Route::post('/feedback-test/{wsm}', [FeedbackTestController::class, 'start'])->name('feedback.test.start');
});

Route::get('/workshops/{workshop}/qr', [WorkshopQrController::class, 'show'])->middleware(['role:admin'])->name('workshop.qr');
Route::post('/workshops/{workshop}/qr/regenerate', [WorkshopQrController::class, 'regenerate'])->middleware(['role:admin'])->name('workshop.qr.regenerate');
Route::get('/qr/{token}', [WorkshopQrController::class, 'scan'])->middleware('throttle:600,1')->where('token', '[A-Za-z0-9]{40}')->name('workshop.scan');

Route::middleware('throttle:600,1')->where(['token' => '[A-Za-z0-9]{40}'])->group(function () {
    Route::get('/qr/{token}/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('/qr/{token}/feedback', [FeedbackController::class, 'storeRating'])->name('feedback.rating');
    Route::post('/qr/{token}/feedback/vragen', [FeedbackController::class, 'storeQuestions'])->name('feedback.questions');
});

Route::get('/class-dashboard', [ClassDashboardController::class, 'index'])->middleware(['role:admin'])->name('class-dashboard');

Route::get('/export/all-users', [ExportController::class, 'exportAll'])->middleware(['role:admin'])->name('export-all-users');
Route::get('/export/classes/{class}', [ExportController::class, 'exportClass'])->middleware(['role:admin'])->name('export-class');
Route::get('/export/all-classes', [ExportController::class, 'exportAllClasses'])->middleware(['role:admin'])->name('export-all-classes');
Route::get('/export/{workshopName}', [ExportController::class, 'exportWorkshop'])->middleware(['role:admin'])->name('export-workshop');

Route::get('/files/download/{file}', [FileController::class, 'download'])->middleware(['role:admin'])->name('download-export')->where('file', '[^/]+');
Route::delete('/files/delete/{file}', [FileController::class, 'delete'])->middleware(['role:admin'])->name('delete-export')->where('file', '[^/]+');

require __DIR__.'/auth.php';
