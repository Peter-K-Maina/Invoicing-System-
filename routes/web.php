<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\MpesaC2BController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect '/' to dashboard if authenticated
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard view
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Protected routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Client and Invoice resources
    Route::resource('clients', ClientController::class);
    Route::resource('invoices', InvoiceController::class);

    // Invoice additional actions
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::post('/{invoice}/send', [InvoiceController::class, 'send'])->name('send');
        Route::post('/{invoice}/pay', [InvoiceController::class, 'pay'])->name('pay');
        Route::patch('/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('mark-paid');
        Route::patch('/{invoice}/mark-pending', [InvoiceController::class, 'markPending'])->name('mark-pending');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'download'])->name('download');
    });

    // M-Pesa STK Push routes
    Route::prefix('mpesa')->name('mpesa.')->group(function () {
        // STK Push and Status
        Route::post('/stk-push', [MpesaController::class, 'stkPush'])->name('stk-push');
        Route::post('/callback', [MpesaController::class, 'callback'])->name('callback');
        Route::get('/query-status/{invoice}', [MpesaController::class, 'queryStatus'])->name('query.status');
        
        // Demo/Testing routes
        Route::post('/demo-pay/{invoice}', [MpesaController::class, 'demoPay'])->name('demo.pay');
        
        // C2B Integration routes
        Route::prefix('c2b')->name('c2b.')->group(function () {
            Route::get('/register', [MpesaC2BController::class, 'registerC2B'])->name('register');
            Route::post('/validate', [MpesaC2BController::class, 'validateC2B'])->name('validate');
            Route::post('/confirm', [MpesaC2BController::class, 'confirmC2B'])->name('confirm');
            Route::post('/demo/{invoice}', [MpesaC2BController::class, 'simulateDemo'])->name('demo');
        });
    });
});

require __DIR__.'/auth.php';

