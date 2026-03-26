<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\PaimentController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'update']);

    // Payment
    Route::post('/reservations/{reservation}/pay', [PaimentController::class, 'pay']);
    Route::get('/reservations/{reservation}/ticket', [PaimentController::class, 'ticket']);

    Route::middleware('admin')->group(function () {
        Route::get('/users', [AuthController::class, 'getAll']);
        Route::put('/users/ban/{id}', [AuthController::class, 'ban']);
        Route::put('/users/unban/{id}', [AuthController::class, 'unban']);

        Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
        Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
    });


    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });

    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::get('/movies', [MovieController::class, 'index']);

    // Rooms
    Route::get('/rooms', [\App\Http\Controllers\Api\RoomController::class, 'index']);
    Route::get('/rooms/{id}', [\App\Http\Controllers\Api\RoomController::class, 'show']);
    Route::middleware('admin')->group(function () {
        Route::post('/rooms', [\App\Http\Controllers\Api\RoomController::class, 'store']);
        Route::put('/rooms/{id}', [\App\Http\Controllers\Api\RoomController::class, 'update']);
        Route::delete('/rooms/{id}', [\App\Http\Controllers\Api\RoomController::class, 'destroy']);
    });

    // Sessions
    Route::get('/sessions', [\App\Http\Controllers\Api\SessionController::class, 'index']);
    Route::get('/sessions/{id}', [\App\Http\Controllers\Api\SessionController::class, 'show']);
    Route::get('/sessions/{id}/available-seats', [\App\Http\Controllers\Api\SessionController::class, 'availableSeats']);
    Route::middleware('admin')->group(function () {
        Route::post('/sessions', [\App\Http\Controllers\Api\SessionController::class, 'store']);
        Route::put('/sessions/{id}', [\App\Http\Controllers\Api\SessionController::class, 'update']);
        Route::delete('/sessions/{id}', [\App\Http\Controllers\Api\SessionController::class, 'destroy']);
    });

    // Reservations
    Route::get('/reservations', [\App\Http\Controllers\Api\ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [\App\Http\Controllers\Api\ReservationController::class, 'show']);
    Route::post('/reservations', [\App\Http\Controllers\Api\ReservationController::class, 'store']);
    Route::middleware('admin')->group(function () {
        Route::put('/reservations/{id}', [\App\Http\Controllers\Api\ReservationController::class, 'update']);
        Route::delete('/reservations/{id}', [\App\Http\Controllers\Api\ReservationController::class, 'destroy']);
    });
});

