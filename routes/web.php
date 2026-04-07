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

// Redirect root
Route::get('/', function () {
    if (Auth::check() && Auth::user()->isStaff()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Redirect generic dashboard
Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->isStaff()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
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

    // Pengurus (Staff Level 2) Management - Only Super Admin
    Route::resource('pengurus', Admin\PengurusController::class)->except(['show'])->parameters(['pengurus' => 'staff'])->middleware('super_admin');

    // Bills Management
    Route::resource('bills', Admin\BillController::class)->except(['show']);
    Route::post('bills/{bill}/mark-paid', [Admin\BillController::class, 'markPaid'])->name('bills.mark-paid');

    // Payments
    Route::resource('payments', Admin\PaymentController::class)->except(['create', 'store']);
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
    Route::get('reports/export-financial', [Admin\ReportController::class, 'exportFinancial'])->name('reports.export-financial');
    Route::get('reports/export-residents', [Admin\ReportController::class, 'exportResidents'])->name('reports.export-residents');

    // Documents
    Route::resource('documents', Admin\DocumentController::class)->except(['show', 'edit', 'update']);
    Route::get('documents/{document}/download', [Admin\DocumentController::class, 'download'])->name('documents.download');

    // Announcements
    Route::resource('announcements', Admin\AnnouncementController::class)->except(['show']);

    // Change Requests (admin approval for pengurus edits)
    Route::get('change-requests', [Admin\ChangeRequestController::class, 'index'])->name('change-requests.index')->middleware('super_admin');
    Route::post('change-requests/{changeRequest}/approve', [Admin\ChangeRequestController::class, 'approve'])->name('change-requests.approve')->middleware('super_admin');
    Route::post('change-requests/{changeRequest}/reject', [Admin\ChangeRequestController::class, 'reject'])->name('change-requests.reject')->middleware('super_admin');
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

    // Documents and Announcements
    Route::get('/dokumen', [Resident\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/dokumen/{document}/download', [Resident\DocumentController::class, 'download'])
        ->middleware('throttle:20,1')
        ->name('documents.download');
    Route::get('/berita', [Resident\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/berita/{announcement}', [Resident\AnnouncementController::class, 'show'])->name('announcements.show');
});

require __DIR__.'/auth.php';
