<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'getMe']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware(['role:' . RoleEnum::ADMIN->value])->group(function () {
        
        Route::resource('category', CategoryController::class);
    });
});
