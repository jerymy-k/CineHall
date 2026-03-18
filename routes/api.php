<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SessionController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password/{token}', [AuthController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::patch('/update/profile', [AuthController::class, 'update']);
    // Route::delete('/delete/user/{id}', [AuthController::class, 'destroy']);
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AuthController::class, 'getAll']);
        Route::patch('/admin/users/ban/{id}', [AuthController::class, 'ban']);
        Route::patch('/admin/users/unban/{id}', [AuthController::class, 'unban']);

        Route::post('admin/movie', [MovieController::class, 'store']);
        Route::patch('admin/movie/update/{id}', [MovieController::class, 'update']);
        Route::delete('admin/movie/delete/{id}', [MovieController::class, 'destroy']);
        Route::post('/sessions', [SessionController::class, 'store']);
    });

    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::get('/movies', [MovieController::class, 'index']);


    // Route::get('/movies', [MovieController::class, 'index']);


});