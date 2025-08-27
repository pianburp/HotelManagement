<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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
        Route::get('/bookings/{booking}', [App\Http\Controllers\User\BookingController::class, 'showBooking'])->name('bookings.show');
        Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\User\BookingController::class, 'cancel'])->name('bookings.cancel');
        
        // Waitlist Routes
        Route::get('/waitlist/create', [App\Http\Controllers\User\WaitlistController::class, 'create'])->name('waitlist.create');
        Route::post('/waitlist', [App\Http\Controllers\User\WaitlistController::class, 'store'])->name('waitlist.store');
        Route::get('/waitlist', [App\Http\Controllers\User\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::delete('/waitlist/{waitlist}', [App\Http\Controllers\User\WaitlistController::class, 'destroy'])->name('waitlist.destroy');
    });

    // Admin Routes
    Route::name('admin.')->prefix('admin')->middleware(['role:admin'])->group(function () {
        // Room Type Management
        Route::resource('room-types', App\Http\Controllers\Admin\RoomTypeController::class);
        
        // Room Management
        Route::resource('rooms', App\Http\Controllers\Admin\RoomController::class);
        
        // Booking Management
        Route::resource('bookings', App\Http\Controllers\Admin\BookingController::class);
        
        // Payment Management
        Route::resource('payments', App\Http\Controllers\Admin\PaymentController::class);
        
        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/occupancy', [App\Http\Controllers\Admin\ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('/reports/revenue', [App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('reports.revenue');
        
        // Waitlist Management
        Route::get('/waitlist', [App\Http\Controllers\Admin\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::patch('/waitlist/{waitlist}/notify', [App\Http\Controllers\Admin\WaitlistController::class, 'notify'])->name('waitlist.notify');
        Route::delete('/waitlist/{waitlist}', [App\Http\Controllers\Admin\WaitlistController::class, 'destroy'])->name('waitlist.destroy');
    });

    // Staff Routes
    Route::name('staff.')->prefix('staff')->middleware(['role:staff|admin'])->group(function () {
        // Room Status Management
        Route::get('/rooms', [App\Http\Controllers\Staff\RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/{room}/edit', [App\Http\Controllers\Staff\RoomController::class, 'edit'])->name('rooms.edit');
        Route::patch('/rooms/{room}', [App\Http\Controllers\Staff\RoomController::class, 'update'])->name('rooms.update');
        
        // Check-in Management
        Route::get('/checkin', [App\Http\Controllers\Staff\CheckInController::class, 'index'])->name('checkin.index');
        Route::get('/checkin/{booking}', [App\Http\Controllers\Staff\CheckInController::class, 'show'])->name('checkin.show');
        Route::post('/checkin/{booking}', [App\Http\Controllers\Staff\CheckInController::class, 'process'])->name('checkin.process');
        
        // Check-out Management
        Route::get('/checkout', [App\Http\Controllers\Staff\CheckOutController::class, 'index'])->name('checkout.index');
        Route::get('/checkout/{booking}', [App\Http\Controllers\Staff\CheckOutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{booking}', [App\Http\Controllers\Staff\CheckOutController::class, 'process'])->name('checkout.process');
        
        // Waitlist Management
        Route::get('/waitlist', [App\Http\Controllers\Staff\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::patch('/waitlist/{waitlist}/notify', [App\Http\Controllers\Staff\WaitlistController::class, 'notify'])->name('waitlist.notify');
    });
});

require __DIR__.'/auth.php';
