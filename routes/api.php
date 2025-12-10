<?php

use App\Http\Controllers\Api\Admin\ProgramController;
use App\Http\Controllers\Api\Admin\ProgramTypesController;
use App\Http\Controllers\Api\Trainer\TAuthController;
use App\Http\Controllers\AuthController;
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

// Trainer routes (public - no auth required)
Route::post('trainer/login', [TAuthController::class, 'login']);
Route::post('trainer/signup', [TAuthController::class, 'signup']);

// Trainer protected routes (requires auth + trainer role)
Route::middleware('auth:sanctum')->middleware('trainer.api')->group(function(){
    Route::put('trainer/{id}', [TAuthController::class, 'update']);           // Edit trainer profile
    Route::get('trainer/{id}', [TAuthController::class, 'show']);             // Get trainer profile
    Route::delete('trainer/{id}', [TAuthController::class, 'destroy']);       // Delete trainer
    Route::get('trainers', [TAuthController::class, 'index']);                // List all trainers
});

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('profile/update', [AuthController::class, 'updateProfile']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);

    Route::get('/program/{program}', [ProgramController::class, 'show'])->name('program.show');
    Route::get('/program', [ProgramController::class, 'index'])->name('program.index');

    Route::post('logout', [AuthController::class, 'logout']);
});
