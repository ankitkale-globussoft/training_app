<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Web\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Web\Admin\PaymentController;
use App\Http\Controllers\Web\Admin\ProgramController;
use App\Http\Controllers\Web\Admin\ProgramTypeController;
use App\Http\Controllers\Web\Admin\TestController;
use App\Http\Controllers\Web\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Web\Org\AuthController as OrgAuthController;
use App\Http\Controllers\Web\Org\ProgramsController;
use App\Http\Controllers\Web\Org\ActiveProgramController;
use App\Http\Controllers\Web\Org\PurchaseController;
use App\Http\Controllers\Web\Trainer\AuthController as TrainerAuthController;
use App\Http\Controllers\Web\Trainer\ContentManagerController;
use App\Http\Controllers\Web\Trainer\DashboardController;
use App\Http\Controllers\Web\Trainer\PaymentController as TrainerPaymentController;
use App\Http\Controllers\Web\Trainer\TrainerController;
use App\Http\Controllers\Web\Trainer\TrainerProgramsController;
use App\Http\Controllers\Web\Trainer\TrainingsController;
use App\Http\Controllers\Web\Student\AuthController as StudentAuthController;
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
    Route::get('forgot-pass', [AdminAuthController::class, 'viewforgotPassword'])->name('view-forgot-pass');
    Route::post('forgot-pass', [AdminAuthController::class, 'sendResetLink'])->name('forgot-pass');

    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');

    Route::get('login', [AdminAuthController::class, 'show_login'])->name('view.login');
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
        Route::get('/trainers/{id}', [AdminTrainerController::class, 'show'])->name('trainers.show');
        Route::post('/trainers/{id}/verify', [AdminTrainerController::class, 'verify'])->name('trainers.verify');
        Route::post('/trainers/{id}/suspend', [AdminTrainerController::class, 'suspend'])->name('trainers.suspend');

        // tests
        Route::resource('test', TestController::class);
        Route::get('test/{id}/questions', [TestController::class, 'getQuestions'])->name('test.questions');
        Route::post('test/question/add', [TestController::class, 'addQuestion'])->name('test.question.add');
        Route::post('test/question/{id}/update', [TestController::class, 'updateQuestion'])->name('test.question.update');
        Route::delete('test/question/{id}/delete', [TestController::class, 'deleteQuestion'])->name('test.question.delete');

        // payments
        Route::get('payments', [PaymentController::class, 'view'])->name('payments');
        Route::get('/payments/data', [PaymentController::class, 'getPaymentsData'])->name('payments.data');
        Route::get('/payments/{id}', [PaymentController::class, 'showPaymentDetails'])->name('payments.details');
        // trainer payments
        Route::get('trainer-payments', [PaymentController::class, 'viewTrainerPayments'])->name('payments.trainer-payments');
        Route::get('trainer-bank-details/{id}', [PaymentController::class, 'getTrainerBankDetails'])->name('payments.bank-details');
        Route::post('update-status', [PaymentController::class, 'updatePaymentStatus'])->name('payments.update-status');

        // blog categories
        Route::controller(App\Http\Controllers\Admin\BlogCategoryController::class)->prefix('blog-categories')->name('blog-categories.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/fetch', 'fetch')->name('fetch');
            Route::get('/fetch-all', 'fetchAll')->name('fetch-all'); // For dropdowns
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::post('/{id}/update', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // blogs
        Route::controller(BlogController::class)->prefix('blogs')->name('blogs.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/fetch', 'fetchBlogs')->name('fetch');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::post('/{id}/update', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

// Trainer Routes --

Route::prefix('trainer')->name('trainer.')->group(function () {
    Route::get('login', [TrainerAuthController::class, 'show_login'])->name('login');
    Route::post('login', [TrainerAuthController::class, 'login'])->name('login');

    Route::get('register', [TrainerAuthController::class, 'show_register'])->name('register');
    Route::post('register', [TrainerAuthController::class, 'register'])->name('register');

    // Password Reset
    Route::get('forgot-password', [App\Http\Controllers\Web\Trainer\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Web\Trainer\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [App\Http\Controllers\Web\Trainer\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [App\Http\Controllers\Web\Trainer\PasswordResetController::class, 'reset'])->name('password.update');

    // Trainer protected routes (requires auth + trainer role)
    Route::middleware('trainer.web')->group(function () {

        Route::get('profile', function () {
            return view('trainer.profile');
        })->name('profile');

        Route::post('profile', [TrainerAuthController::class, 'update'])->name('profile.update');
        Route::post('upload-signed-form', [TrainerAuthController::class, 'uploadSignedForm'])->name('upload-signed-form');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        Route::get('assigned-trainings', [TrainingsController::class, 'assigned_trainings'])->name('trainings.assigned');
        Route::post('/trainings/update-status', [TrainingsController::class, 'updateStatus'])->name('trainings.update_status');

        // Content Manager 
        Route::get('content-manager', [ContentManagerController::class, 'index'])->name('content-manager');
        Route::get('/content-manager/{booking_id}/manage', [ContentManagerController::class, 'manage'])->name('content.manage');
        Route::get('/content-manager/add/{booking_id}', [ContentManagerController::class, 'add'])->name('content.add');
        Route::get('/content-manager/content/{content_id}/edit', [ContentManagerController::class, 'edit'])->name('content.edit');
        Route::post('/content-manager/update', [ContentManagerController::class, 'update'])->name('content.update');
        Route::get('/content-manager/booking/{booking_id}', [ContentManagerController::class, 'getBookingDetails']);
        Route::post('/content-manager/store', [ContentManagerController::class, 'store'])->name('content.store');

        Route::delete('/content-manager/{id}', [ContentManagerController::class, 'destroy'])->name('content.destroy');

        // Payments
        Route::get('account-details', [TrainerPaymentController::class, 'viewAccountDetails'])->name('payments.account-details');
        Route::post('account-details', [TrainerPaymentController::class, 'storeAccountDetails'])->name('payments.account-details.store');
        Route::get('payments', [TrainerPaymentController::class, 'view'])->name('payments.view');
        Route::get('payment/request', [TrainerPaymentController::class, 'requestPayment'])->name('payments.request');
        Route::post('payment/request', [TrainerPaymentController::class, 'storeRequest'])->name('payments.request.store');

        Route::get('logout', [TrainerAuthController::class, 'logout'])->name('logout');
    });
});


// Organisation Routes
Route::prefix('org')->name('org.')->group(function () {
    Route::get('register', [OrgAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [OrgAuthController::class, 'register'])->name('register.store');

    Route::get('login', [OrgAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [OrgAuthController::class, 'login'])->name('login');

    // Password Reset
    Route::get('forgot-password', [App\Http\Controllers\Web\Org\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Web\Org\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [App\Http\Controllers\Web\Org\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [App\Http\Controllers\Web\Org\PasswordResetController::class, 'reset'])->name('password.update');

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

        // Active Programs
        Route::get('active-programs', [ActiveProgramController::class, 'index'])->name('active-programs.index');
        Route::get('active-programs/{booking_id}/content', [ActiveProgramController::class, 'viewContent'])->name('active-programs.content');
        Route::get('active-programs/trainer/{trainer_id}', [ActiveProgramController::class, 'showTrainer'])->name('active-programs.trainer');

        // Purchases
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/{booking_id}/invoice', [PurchaseController::class, 'invoice'])->name('purchases.invoice');

        // Student Management
        Route::controller(App\Http\Controllers\Web\Org\StudentController::class)->prefix('students')->name('students.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/store', 'store')->name('store');
            Route::post('/{id}/update', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::post('/import', 'import')->name('import');
        });

        Route::get('logout', [OrgAuthController::class, 'logout'])->name('logout');
    });
});

Route::prefix('student')->name('student.')->group(function () {
    Route::get('login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [StudentAuthController::class, 'login'])->name('login');

    // Student protected routes
    Route::middleware('student.web')->group(function () {
        Route::get('home', function () {
            return view('student.home');
        })->name('home');

        // Test Routes
        Route::controller(App\Http\Controllers\Web\Student\TestController::class)->prefix('tests')->name('tests.')->group(function () {
            Route::get('/available', 'available')->name('available');
            Route::get('/{test_id}/attempt', 'show')->name('show');
            Route::get('/{test_id}/data', 'getTest')->name('data');
            Route::post('/submit', 'submit')->name('submit');
            Route::get('/attempted', 'attempted')->name('attempted');
            Route::get('/result/{attempt_id}', 'result')->name('result');
        });

        // Certificate Routes
        Route::controller(App\Http\Controllers\Web\Student\CertificateController::class)->prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{attempt_id}/view', 'show')->name('view');
        });

        Route::get('logout', [StudentAuthController::class, 'logout'])->name('logout');
    });
});