<?php

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
        Route::get('/users  ', [AuthController::class, 'getAll']);
        Route::put('/users/ban/{id}', [AuthController::class, 'ban']);
        Route::put('/users/unban/{id}', [AuthController::class, 'unban']);

        Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
        Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
    });

    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::get('/movies', [MovieController::class, 'index']);
});