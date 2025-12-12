<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Web\Admin\PaymentController;
use App\Http\Controllers\Web\Admin\ProgramController;
use App\Http\Controllers\Web\Admin\TestController;
use App\Http\Controllers\Web\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Web\Trainer\AuthController as TrainerAuthController;
use App\Http\Controllers\Web\Trainer\TrainerController;
use Illuminate\Support\Facades\Route;


Route::get('trainer/signup', function () {
    return view('trainer.signup');
});

// Admin Routes -- 
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('login', [AdminAuthController::class, 'show_login'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');

    // Admin protected routes
    Route::middleware(['admin'])->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('program', ProgramController::class);

        // trainers
        Route::get('trainers', [AdminTrainerController::class, 'view'])->name('trainers');
        Route::get('/trainers/list', [AdminTrainerController::class, 'list'])->name('trainers.list');
        Route::get('/trainers/{id}', [TrainerController::class, 'show'])->name('trainers.show');
        
        // tests
        Route::resource('test', TestController::class);

        // payments
        Route::get('payments', [PaymentController::class, 'view'])->name('payments');
    });
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