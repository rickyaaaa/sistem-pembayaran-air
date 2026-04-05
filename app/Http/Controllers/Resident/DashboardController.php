<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;

class DashboardController extends Controller
{
    public function index()
    {
        // Financial Summary (current year)
        $year = now()->year;

        $totalIncome = Payment::where('status', 'confirmed')
            ->whereYear('payment_date', $year)
            ->sum('amount_paid');

        $totalRegistrations = Registration::whereYear('payment_date', $year)
            ->sum('amount');

        $totalExpenses = Expense::whereYear('date', $year)
            ->sum('amount');

        $currentBalance = ($totalIncome + $totalRegistrations) - $totalExpenses;

        // Last 5 expenses (with receipt link)
        $recentExpenses = Expense::orderByDesc('date')
            ->limit(5)
            ->get();

        return view('resident.dashboard', compact(
            'totalIncome',
            'totalRegistrations',
            'totalExpenses',
            'currentBalance',
            'recentExpenses',
            'year',
        ));
    }
}
