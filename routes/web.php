<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Web\Admin\PaymentController;
use App\Http\Controllers\Web\Admin\ProgramController;
use App\Http\Controllers\Web\Admin\ProgramTypeController;
use App\Http\Controllers\Web\Admin\TestController;
use App\Http\Controllers\Web\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Web\Org\AuthController as OrgAuthController;
use App\Http\Controllers\Web\Org\ProgramsController;
use App\Http\Controllers\Web\Trainer\AuthController as TrainerAuthController;
use App\Http\Controllers\Web\Trainer\TrainerController;
use App\Http\Controllers\Web\Trainer\TrainerProgramsController;
use App\Http\Controllers\Web\Trainer\TrainingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;


Route::get('/send-test-mail/{email}', function ($email) {
    Mail::to($email)->send(new TestMail());
    return "Mail sent successfully to $email!";
});

Route::get('/', function () {
    return view('home');
})->name('home');

// Admin Routes -- 
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('forgot-pass', [AdminAuthController::class, 'viewforgotPassword'])->name('forgot-pass');
    Route::post('forgot-pass', [AdminAuthController::class, 'sendResetLink'])->name('forgot-pass');

    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');

    Route::get('login', [AdminAuthController::class, 'show_login'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');

    // Admin protected routes
    Route::middleware(['admin'])->group(function () {

        Route::get('profile', function () {
            return view('admin.profile');
        })->name('profile');

        Route::post('profile', [AdminAuthController::class, 'update'])->name('profile.update');

        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // program type
        Route::resource('program-types', ProgramTypeController::class);
        Route::get('program-types-list', [ProgramTypeController::class, 'list'])->name('program-types.list');

        // program
        Route::resource('program', ProgramController::class);

        // trainers
        Route::get('trainers', [AdminTrainerController::class, 'view'])->name('trainers');
        Route::get('/trainers/list', [AdminTrainerController::class, 'list'])->name('trainers.list');
        Route::get('/trainers/{id}', [TrainerController::class, 'show'])->name('trainers.show');

        // tests
        Route::resource('test', TestController::class);

        // payments
        Route::get('payments', [PaymentController::class, 'view'])->name('payments');
        Route::get('/payments/data', [PaymentController::class, 'getPaymentsData'])->name('payments.data');
        Route::get('/payments/{id}', [PaymentController::class, 'showPaymentDetails'])->name('payments.details');
        
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

// Trainer Routes --

Route::prefix('trainer')->name('trainer.')->group(function () {
    Route::get('login', [TrainerAuthController::class, 'show_login'])->name('login');
    Route::post('login', [TrainerAuthController::class, 'login'])->name('login');

    Route::get('register', [TrainerAuthController::class, 'show_register'])->name('register');
    Route::post('register', [TrainerAuthController::class, 'register'])->name('register');

    // Trainer protected routes (requires auth + trainer role)
    Route::middleware('trainer.web')->group(function () {

        Route::get('profile', function () {
            return view('trainer.profile');
        })->name('profile');

        Route::post('profile', [TrainerAuthController::class, 'update'])->name('profile.update');

        Route::get('dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');

        // Trainer Programs
        Route::get('my-programs', [TrainerProgramsController::class, 'browse'])->name('programs.browse');
        Route::get('my-programs/list', [TrainerProgramsController::class, 'list'])->name('programs.list');
        Route::post('my-programs/select', [TrainerProgramsController::class, 'select'])->name('programs.select');
        Route::delete('my-programs/remove', [TrainerProgramsController::class, 'remove'])->name('programs.remove');
        Route::get('selected-programs', [TrainerProgramsController::class, 'index'])->name('programs.index');

        // Trainings
        Route::get('open-trainings', [TrainingsController::class, 'open_trainings'])->name('trainings.open');
        Route::post('/trainings/accept', [TrainingsController::class, 'acceptTraining'])->name('trainings.accept');
        Route::get('upcomming-trainings', [TrainingsController::class, 'upcomming'])->name('trainings.upcomming');
        Route::get('upcomming-trainings/list', [TrainingsController::class, 'list'])->name('trainings.upcomming.list');



        Route::get('logout', [TrainerAuthController::class, 'logout'])->name('logout');
    });
});


// Organisation Routes
Route::prefix('org')->name('org.')->group(function () {
    Route::get('register', [OrgAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [OrgAuthController::class, 'register'])->name('register.store');

    Route::get('login', [OrgAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [OrgAuthController::class, 'login'])->name('login');

    // Org protected routes
    Route::middleware('org.web')->group(function () {
        Route::get('home', function () {
            return view('organisation.home');
        })->name('home');

        // profile
        Route::get('profile', [OrgAuthController::class, 'profile'])->name('profile');
        Route::post('profile/update', [OrgAuthController::class, 'update'])->name('profile.update');

        // programs
        Route::get('programs', [ProgramsController::class, 'index'])->name('programs.index');
        Route::get('/programs/{id}', [ProgramsController::class, 'show'])->name('programs.show');
        Route::post('/programs/request', [ProgramsController::class, 'requestProgram'])->name('programs.request');

        Route::get('requestedPrograms', [ProgramsController::class, 'show_requestedPrograms'])->name('programs.view.requested');
        Route::delete('/programs/request/{id}', [ProgramsController::class, 'cancelRequest'])->name('programs.request.cancel');

        // payment
        Route::post('/programs/payment/initiate', [ProgramsController::class, 'initiatePayment'])->name('programs.payment.initiate');
        Route::post('/programs/payment/verify', [ProgramsController::class, 'verifyPayment'])->name('programs.payment.verify');

        Route::get('logout', [OrgAuthController::class, 'logout'])->name('logout');
    });
});