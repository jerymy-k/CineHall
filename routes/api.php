<?php

// use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'store']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password/{token}', [AuthController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::patch('/update/profile', [AuthController::class, 'update']);
    Route::delete('/delete/user/{id}', [AuthController::class, 'destroy']);
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AuthController::class, 'getAll']);
        Route::patch('/admin/users/ban/{id}', [AuthController::class, 'ban']);
        Route::patch('/admin/users/unban/{id}', [AuthController::class, 'unban']);
    });


});