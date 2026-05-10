<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register/confirm', [AuthController::class, 'confirmRegister'])->name('confirm-register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::group(['prefix' => 'profile', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [UserController::class, 'getProfile'])->name('profile.get');
    Route::put('/update', [UserController::class, 'updateProfile'])->name('profile.update');
});

Route::group(['prefix' => 'dormitories', 'middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'rooms'], function () {
        Route::get('/', [RoomController::class, 'rooms'])->name('dormitories.rooms.list')->middleware('can_do:rooms.view');
    });
});

// e thấy cái này chưa chuẩn rest cho lắm, có nên để theo kiểu users/{id}/dormitories/{dormitory_id}/rooms ko a, e sợ dài quá check policy hơi khó nên e để tạm:))
Route::group(['prefix' => 'rooms', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [RoomController::class, 'rooms'])->name('rooms.list')->middleware('can_do:rooms.view');
    Route::post('/', [RoomController::class, 'createRoom'])->name('rooms.create')->middleware('can_do:rooms.create');
    Route::put('/{room}', [RoomController::class, 'updateRoom'])->name('rooms.update')->middleware('can_do:rooms.update');
});



Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'users'])->name('users.list')->middleware('can_do:users.view');
        Route::delete('/{user}', [UserController::class, 'deleteUser'])->name('users.delete')->middleware('can_do:users.delete');
        Route::put('/{user}/roles', [RoleController::class, 'updateUserRoles'])->name('users.update.roles')->middleware('can_do:users.update.roles');
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RoleController::class, 'list'])->name('roles.list')->middleware('super_admin');
        Route::put('/{user}', [UserRoleController::class, 'updateUserRoles'])->name('roles.user')->middleware('can_do:users.update.roles');
    });
});
