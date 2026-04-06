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

// Redirect root to public resident dashboard
Route::get('/', function () {
    if (Auth::check() && Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('resident.dashboard');
});

// Redirect generic dashboard
Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('resident.dashboard');
})->name('dashboard');

// Change password (all authenticated roles)
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
    Route::resource('residents', Admin\ResidentController::class)->except(['show'])->middleware('throttle:30,1');

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
    Route::get('expenses/{expense}/proof', [Admin\ExpenseController::class, 'viewProof'])->name('expenses.proof');

    // Registrations
    Route::resource('registrations', Admin\RegistrationController::class)->only(['index', 'create', 'store', 'destroy']);

    // Reports
    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
});

/*
|--------------------------------------------------------------------------
| Resident Routes (PUBLIC - No authentication required)
|--------------------------------------------------------------------------
*/
Route::prefix('warga')->name('resident.')->group(function () {
    // Public Dashboard (financial summary + expense list)
    Route::get('/dashboard', [Resident\DashboardController::class, 'index'])->name('dashboard');

    // Bill Search by house number
    Route::get('/tagihan', [Resident\BillController::class, 'index'])
        ->middleware('throttle:30,1')
        ->name('bills.index');
    Route::get('/tagihan/{bill}', [Resident\BillController::class, 'show'])->name('bills.show');

    // Payment
    Route::get('/bayar/{bill}', [Resident\PaymentController::class, 'create'])->name('payments.create');
    Route::post('/bayar/{bill}', [Resident\PaymentController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('payments.store');
    Route::get('/riwayat-bayar', [Resident\PaymentController::class, 'history'])
        ->middleware('throttle:30,1')
        ->name('payments.history');

    // Public route to view expense proof (read-only, no auth)
    Route::get('/pengeluaran/{expense}/bukti', [Resident\ExpenseProofController::class, 'show'])
        ->middleware('throttle:30,1')
        ->name('expenses.proof');
});

require __DIR__.'/auth.php';
