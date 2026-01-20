<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InstrumentConditionController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'getMe']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {

    //Endpoint Role Admin
    Route::middleware(['role:' . RoleEnum::ADMIN->value])->group(function () {

        //route Kategori
        Route::get('category/no-paginate', [CategoryController::class, 'noPaginate'])->name('category-no-paginate');
        Route::resource('category', CategoryController::class);

        //Route Instrument
        Route::get('instrument/no-paginate', [InstrumentController::class, 'noPaginate'])->name('instrument-no-paginate');
        Route::post('instrument/{id}', [InstrumentController::class, 'update'])->name('instrument-update');
        Route::resource('instrument', InstrumentController::class);
    });

    //Endpoint Role Staff
    Route::middleware(['role:' . RoleEnum::STAFF->value . '|' . RoleEnum::ADMIN->value])->group(function () {
        Route::put('/rental/{id}/status', [RentalController::class, 'statusRental'])->name('rental-status');

        Route::get('/instrument-condition/no-paginate',[InstrumentConditionController::class, 'noPaginate'])->name('instrument-condition-no-paginate');
        Route::resource('instrument-condition', InstrumentConditionController::class);
    });

    //Endpoint Role Customer
    Route::middleware(['role:' . RoleEnum::CUSTOMER->value . '|' . RoleEnum::ADMIN->value])->group(function () {

        Route::get('rental/no-paginate', [RentalController::class, 'noPaginate'])->name('rental-no-paginate');
        Route::resource('rental', RentalController::class);
    });
});
