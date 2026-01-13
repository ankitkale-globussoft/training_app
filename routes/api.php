<?php

use App\Http\Controllers\Api\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Api\Admin\ProgramTypesController;
use App\Http\Controllers\Api\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Org\ProgramsController as OrgProgramsController;
use App\Http\Controllers\Api\Org\ActiveProgramController;
use App\Http\Controllers\Api\Org\PurchaseController;
use App\Http\Controllers\Api\Trainer\TrainingsController as TrainerTrainingsController;
use App\Http\Controllers\Api\Trainer\ContentManagerController;
use App\Http\Controllers\Api\Trainer\TrainerProgramsController;
use App\Http\Controllers\Api\Trainer\DashboardController as TrainerDashboardController;
use App\Http\Controllers\Api\Org\AuthController as OrgAuthController;
use App\Http\Controllers\Api\Org\StudentController;
use App\Http\Controllers\Api\Trainer\TAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login'])->name('login');

// Admin only routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('dashboard', [AdminDashboardController::class, 'index']);

    Route::resource('programtype', ProgramTypesController::class);
    Route::resource('program', AdminProgramController::class)->except(['show', 'index']);

    // Admin Trainers
    Route::get('trainers', [AdminTrainerController::class, 'index']);
    Route::get('trainers/{id}', [AdminTrainerController::class, 'show']);
    Route::post('trainers/{id}/verify', [AdminTrainerController::class, 'verify']);
    Route::post('trainers/{id}/suspend', [AdminTrainerController::class, 'suspend']);

    // Admin Payments
    Route::get('payments', [AdminPaymentController::class, 'getPaymentsData']);
    Route::get('payments/{id}', [AdminPaymentController::class, 'showPaymentDetails']);
});

// Trainer routes (public - no auth required)
Route::post('trainer/login', [TAuthController::class, 'login']);
Route::post('trainer/signup', [TAuthController::class, 'signup']);

// Trainer protected routes (requires auth + trainer role)
Route::middleware(['auth:sanctum', 'trainer.api'])->group(function () {
    // Route::get('dashboard', [TrainerDashboardController::class, 'index']);

    Route::post('trainer/{id}', [TAuthController::class, 'update']);
    Route::get('trainer/{id}', [TAuthController::class, 'show']);
    Route::post('trainer/upload-signed-form', [TAuthController::class, 'uploadSignedForm'])->name('api.trainer.upload-signed-form');
    //Route::delete('trainer/{id}', [TAuthController::class, 'destroy']);
    //Route::get('trainers', [TAuthController::class, 'index']);

    // Trainings
    Route::get('open-trainings', [TrainerTrainingsController::class, 'open_trainings']);
    Route::post('trainings/accept', [TrainerTrainingsController::class, 'acceptTraining']);
    Route::get('upcoming-trainings', [TrainerTrainingsController::class, 'upcoming']);

    // Content Manager
    Route::get('content-manager', [ContentManagerController::class, 'index']);
    Route::get('content-manager/{booking_id}/manage', [ContentManagerController::class, 'manage']);
    Route::get('content-manager/booking/{booking_id}', [ContentManagerController::class, 'getBookingDetails']);
    Route::post('content-manager', [ContentManagerController::class, 'store']);
    Route::put('content-manager/{id}', [ContentManagerController::class, 'update']);
    Route::delete('content-manager/{id}', [ContentManagerController::class, 'destroy']);

    // Program Selections
    Route::get('programs/browse', [TrainerProgramsController::class, 'browse']);
    Route::get('programs/list', [TrainerProgramsController::class, 'list']);
    Route::post('programs/select', [TrainerProgramsController::class, 'select']);
    Route::post('programs/remove', [TrainerProgramsController::class, 'remove']);
    Route::get('programs/selected', [TrainerProgramsController::class, 'index']);
});

// Organisation Routes
Route::prefix('org/')->group(function () {
    Route::post('login', [OrgAuthController::class, 'login']);
    Route::post('register', [OrgAuthController::class, 'register']);

    Route::middleware(['auth:sanctum', 'org.api'])->group(function () {
        Route::post('profile/update', [OrgAuthController::class, 'update']);

        // Programs
        Route::get('programs', [OrgProgramsController::class, 'index']); // get the programs
        Route::get('programs/{id}', [OrgProgramsController::class, 'show']); // get program detail
        Route::post('programs/request', [OrgProgramsController::class, 'requestProgram']);
        Route::get('requested-programs', [OrgProgramsController::class, 'show_requestedPrograms']);
        Route::delete('programs/request/{id}', [OrgProgramsController::class, 'cancelRequest']);

        // Active Trainings & Content
        Route::get('active-programs', [ActiveProgramController::class, 'index']);
        Route::get('active-programs/{booking_id}/content', [ActiveProgramController::class, 'viewContent']);
        Route::get('active-programs/trainer/{trainer_id}', [ActiveProgramController::class, 'showTrainer']);

        // Purchases & Invoices
        Route::get('purchases', [PurchaseController::class, 'index']);
        Route::get('purchases/{booking_id}/invoice', [PurchaseController::class, 'invoice']);

        // Payments
        Route::post('programs/payment/initiate', [OrgProgramsController::class, 'initiatePayment']);
        Route::post('programs/payment/verify', [OrgProgramsController::class, 'verifyPayment']);

        // Student Management
        Route::controller(StudentController::class)->prefix('students')->group(function () {
            Route::get('/', 'index');
            Route::post('/store', 'store');
            Route::post('/{id}/update', 'update');
            Route::delete('/{id}', 'destroy');
            Route::post('/{id}/toggle-status', 'toggleStatus');
            Route::post('/import', 'import');
        });
    });
});

// User routes (General)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('profile/update', [AuthController::class, 'updateProfile']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']);

    Route::get('/program/{program}', [AdminProgramController::class, 'show'])->name('program.show');
    Route::get('/program', [AdminProgramController::class, 'index'])->name('program.index');

    Route::post('logout', [AuthController::class, 'logout']);
});