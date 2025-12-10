<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Trainer\AuthController as TrainerAuthController;
use App\Http\Controllers\Web\Trainer\TrainerController;
use Illuminate\Support\Facades\Route;


Route::get('trainer/signup', function () {
    return view('trainer.signup');
});

// Admin Routes -- 
Route::prefix('admin')->name('admin.')->group(function(){ 
    // add admin routes here...
});

// Trainer Routes --

Route::prefix('trainer')->name('trainer.')->group(function () {
    Route::get('login', [TrainerAuthController::class, 'show_login'])->name('login');
    Route::post('login', [TrainerAuthController::class, 'login'])->name('login');
    
    Route::get('register', [TrainerAuthController::class, 'show_register'])->name('register');
    Route::post('register', [TrainerAuthController::class, 'register'])->name('register');
    
    // Trainer protected routes (requires auth + trainer role)
    Route::middleware('trainer.web')->group(function(){
        Route::get('dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');
        Route::get('logout', [TrainerAuthController::class, 'logout'])->name('logout');
    });

});