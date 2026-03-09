<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\WorkshopDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Success;
use App\Http\Controllers\MailController;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Route;
use App\Models\Workshop;
use App\Models\Bookings;

Route::get('/', function () {
    return redirect()->route('register');
});

Route::get('/dashboard', function () {
    return view('dashboard')->with("workshops", Workshop::all());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/viewCapacity', [BookingController::class, 'viewCapacity'])->name('viewCapacity');

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

Route::get('/students-overview', [UserController::class, 'index'])->middleware(['role:admin'])->name('students-overview');
Route::get('/edit-student/{id}', [UserController::class, 'edit'])->middleware(['role:admin'])->name('edit-student');
Route::patch('/edit-student/{id}/bookings', [UserController::class, 'updateBookings'])->middleware(['role:admin'])->name('edit-student.bookings.update');


Route::get('/workshop-dashboard', [WorkshopDashboardController::class, 'index'])->middleware(['role:admin'])->name('workshop-dashboard');
Route::get('/workshop-moment/{wsm}', [WorkshopDashboardController::class, 'showbookings'])->name('workshop-moment.showbookings');
Route::get('/workshop-moment/{wsm}/{class}', [WorkshopDashboardController::class, 'showfilteredbookings'])->name('workshop-moment.showfilteredbookings');

/*Route::get('/bookings', function () {

    //return Bookings::with('student','workshopMoment')->get();
    return json_encode(Bookings::with(['student', 'workshopMoment.workshop', 'workshopMoment.moment'])->get());
});*/

Route::get('/moments', function () {
    //return Bookings::with('student','workshopMoment')->get();
    return json_encode(Moment::get());
});

require __DIR__.'/auth.php';
