<?php

use App\Http\Controllers\ApiBookingsController;
use App\Http\Controllers\ApiClassesController;
use App\Http\Controllers\ApiSettingController;
use App\Http\Controllers\ApiStatisticsController;
use App\Http\Controllers\ApiUserController;
use App\Http\Controllers\ApiWorkshopController;
use App\Http\Controllers\ApiWorkshopMomentController;
use Illuminate\Support\Facades\Route;

// De api draait op dezelfde site, dus we gebruiken de gewone sessie login (web middleware).
// Wie mag wat staat in de policies (app/Policies) en in de request classes.
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // student
    Route::apiResource('workshops', ApiWorkshopController::class)->only(['index']);
    Route::apiResource('workshop-moments', ApiWorkshopMomentController::class)->only(['index', 'show']);
    Route::apiResource('bookings', ApiBookingsController::class)->only(['store']);

    // admin
    Route::apiResource('users', ApiUserController::class)->only(['index', 'show']);
    Route::patch('users/{user}/bookings', [ApiUserController::class, 'updateBookings'])->name('users.bookings.update');
    Route::apiResource('classes', ApiClassesController::class)->only(['index']);
    Route::post('settings/toggle-signups', [ApiSettingController::class, 'toggleSignups'])->name('settings.toggle-signups');
    Route::get('statistics', [ApiStatisticsController::class, 'show'])->name('statistics.show');
});
