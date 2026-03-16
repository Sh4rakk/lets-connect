<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\WorkshopDashboardController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\UserExportController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Success;
use App\Http\Controllers\MailController;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Route;
use App\Models\Workshop;
use App\Models\Bookings;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $signupsOpen = Setting::where('key', 'signups_open')->first();
    $registrationsClosed = !$signupsOpen || $signupsOpen->value === '0';

    return $registrationsClosed
        ? redirect()->route('login')
        : redirect()->route('register');
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

Route::get('/viewCapacity', [BookingController::class, 'viewCapacity'])->name('viewCapacity');
Route::get('/Capacity', [BookingController::class, 'viewRoundCapacity'])->name('Capacity');

Route::get('/send-mail', [MailController::class, 'store']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/save', [BookingController::class, 'bookWorkshop'])->middleware(['auth', 'verified']);


Route::get('/overzicht', function () {
    return view('overzicht');
});

Route::middleware([Success::class])->get('/success', function () {
    return view('success');
});

Route::get('/workshop', function () {
    return json_encode(Workshop::all());
});

Route::get('/admin-dashboard', function () {
    return view('dashboard.selection');
})->middleware(['role:admin'])->name('selectionDashboard');

Route::post('/toggle-signups', [WorkshopDashboardController::class, 'toggleSignups'])->middleware(['role:admin']);

Route::get('/students-overview', [UserController::class, 'index'])->middleware(['role:admin'])->name('students-overview');
Route::get('/edit-student/{id}', [UserController::class, 'edit'])->middleware(['role:admin'])->name('edit-student');
Route::patch('/edit-student/{id}/bookings', [UserController::class, 'updateBookings'])->middleware(['role:admin'])->name('edit-student.bookings.update');


Route::get('/workshop-dashboard', [WorkshopDashboardController::class, 'index'])->middleware(['role:admin'])->name('workshop-dashboard');
Route::get('/workshop-moment/{wsm}', [WorkshopDashboardController::class, 'showbookings'])->middleware(['role:admin'])->name('workshop-moment.showbookings');
Route::get('/workshop-moment/{wsm}/{class}', [WorkshopDashboardController::class, 'showfilteredbookings'])->middleware(['role:admin'])->name('workshop-moment.showfilteredbookings');
Route::get('/workshop/{workshopName}/export', [WorkshopDashboardController::class, 'exportWorkshop'])->middleware(['role:admin'])->name('workshop.export');

/*Route::get('/bookings', function () {

    //return Bookings::with('student','workshopMoment')->get();
    return json_encode(Bookings::with(['student', 'workshopMoment.workshop', 'workshopMoment.moment'])->get());
});*/

Route::get('/moments', function () {
    //return Bookings::with('student','workshopMoment')->get();
    return json_encode(Moment::get());
});

Route::get('/export-users', [UserExportController::class, 'export'])
    ->name('users.export');


// Two-factor authentication routes
Route::middleware(['auth'])->group(function () {
    Route::get('/two-factor/challenge', [TwoFactorAuthController::class, 'showChallenge'])
        ->name('two-factor.challenge');

    Route::post('/two-factor/verify', [TwoFactorAuthController::class, 'verify'])
        ->name('two-factor.verify');

    Route::post('/two-factor/resend', [TwoFactorAuthController::class, 'resend'])
        ->name('two-factor.resend');
});

require __DIR__.'/auth.php';
