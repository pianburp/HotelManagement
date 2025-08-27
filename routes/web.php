<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Routes
    Route::name('user.')->prefix('user')->group(function () {
        // Room Routes
        Route::get('/rooms', [App\Http\Controllers\User\BookingController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/search', [App\Http\Controllers\User\BookingController::class, 'search'])->name('rooms.search');
        Route::get('/rooms/{room}', [App\Http\Controllers\User\BookingController::class, 'show'])->name('rooms.show');
        
        // Booking Routes
        Route::get('/bookings/create', [App\Http\Controllers\User\BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [App\Http\Controllers\User\BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings', [App\Http\Controllers\User\BookingController::class, 'history'])->name('bookings.history');
        Route::get('/bookings/{booking}', [App\Http\Controllers\User\BookingController::class, 'show'])->name('bookings.show');
        
        // Waitlist Routes
        Route::get('/waitlist/create', [App\Http\Controllers\User\WaitlistController::class, 'create'])->name('waitlist.create');
        Route::post('/waitlist', [App\Http\Controllers\User\WaitlistController::class, 'store'])->name('waitlist.store');
        Route::get('/waitlist', [App\Http\Controllers\User\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::delete('/waitlist/{waitlist}', [App\Http\Controllers\User\WaitlistController::class, 'destroy'])->name('waitlist.destroy');
    });
});

require __DIR__.'/auth.php';
