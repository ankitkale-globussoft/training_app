<?php

use App\Http\Controllers\Web\Trainer\TAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('trainer.dashboard');
})->name('dashboard');

Route::get('trainer/signup', function () {
    return view('trainer.signup');
});

Route::get('trainer/login', [TAuthController::class, 'show_login']);
Route::post('trainer/login', [TAuthController::class, 'login']);