<?php

use App\Http\Controllers\Api\RoomAvailabilityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Room availability API routes
Route::prefix('rooms')->group(function () {
    Route::get('{room}/availability', [RoomAvailabilityController::class, 'getAvailableDates'])
        ->name('api.rooms.availability');
    
    Route::post('{room}/check-availability', [RoomAvailabilityController::class, 'checkAvailability'])
        ->name('api.rooms.check-availability');
});
