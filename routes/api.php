<?php

use App\Enums\UserRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::group(['prefix' => 'profile', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [UserController::class, 'users']);
    Route::put('/update', [UserController::class, 'updateProfile']);
});

Route::middleware('role:' . UserRole::SUPER_ADMIN->value)->group(function () {
    Route::get('/users', [UserController::class, 'users']);
    Route::get('/my-secret-rooms', [AuthController::class, 'index']);
});
