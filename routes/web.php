<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\Resident;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('resident.dashboard');
    }
    return redirect()->route('login');
});

// Redirect generic dashboard
Route::get('/dashboard', function () {
    if (Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('resident.dashboard');
})->middleware('auth')->name('dashboard');

// Change password (all roles)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Residents Management
    Route::resource('residents', Admin\ResidentController::class)->except(['show']);
    Route::post('residents/{resident}/reset-password', [Admin\ResidentController::class, 'resetPassword'])
        ->name('residents.reset-password');

    // Bills Management
    Route::resource('bills', Admin\BillController::class)->except(['show']);

    // Payment Confirmation
    Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/confirm', [Admin\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('payments/{payment}/reject', [Admin\PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('payments/{payment}/proof', [Admin\PaymentController::class, 'viewProof'])->name('payments.proof');

    // Expenses
    Route::resource('expenses', Admin\ExpenseController::class)->except(['show']);

    // Registrations
    Route::resource('registrations', Admin\RegistrationController::class)->only(['index', 'create', 'store', 'destroy']);

    // Reports
    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
});

/*
|--------------------------------------------------------------------------
| Resident Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'resident'])->prefix('warga')->name('resident.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Resident\DashboardController::class, 'index'])->name('dashboard');

    // Bills
    Route::get('/tagihan', [Resident\BillController::class, 'index'])->name('bills.index');
    Route::get('/tagihan/{bill}', [Resident\BillController::class, 'show'])->name('bills.show');

    // Payment
    Route::get('/bayar/{bill}', [Resident\PaymentController::class, 'create'])->name('payments.create');
    Route::post('/bayar/{bill}', [Resident\PaymentController::class, 'store'])->name('payments.store');
    Route::get('/riwayat-bayar', [Resident\PaymentController::class, 'history'])->name('payments.history');
});

require __DIR__.'/auth.php';
