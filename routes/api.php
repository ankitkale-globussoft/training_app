<?php

use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ProgramTypesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Trainer\TAuthController;
use Faker\Guesser\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login'])->name('login');

// Admin only routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::resource('programtype', ProgramTypesController::class);
    Route::resource('program', ProgramController::class)->except(['show', 'index']);
});

// Trainer routes

Route::post('trainer/login', [TAuthController::class, 'login']);
Route::resource('tauth', TAuthController::class);


// to be fixed...
Route::middleware(['auth:sanctum', 'trainer'])->group(function(){
});

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('profile/update', [AuthController::class, 'updateProfile']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);

    Route::get('/program/{program}', [ProgramController::class, 'show'])->name('program.show');
    Route::get('/program', [ProgramController::class, 'index'])->name('program.index');

    Route::post('logout', [AuthController::class, 'logout']);
});
