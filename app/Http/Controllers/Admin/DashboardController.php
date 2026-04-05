<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Resident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Total income from confirmed payments this year
        $totalIncome = Payment::where('status', 'confirmed')
            ->whereYear('payment_date', $year)
            ->sum('amount_paid');

        // Total expenses this year
        $totalExpenses = Expense::whereYear('date', $year)->sum('amount');

        // Total registration fees this year
        $totalRegistrations = Registration::whereYear('payment_date', $year)->sum('amount');

        // Current balance
        $currentBalance = ($totalIncome + $totalRegistrations) - $totalExpenses;

        // Monthly income breakdown
        $monthlyIncome = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyIncome[$m] = Payment::where('status', 'confirmed')
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('amount_paid');
        }

        // Pending payments count
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Total residents
        $totalResidents = Resident::where('is_active', true)->count();

        // Unpaid bills this month
        $unpaidBills = Bill::where('status', 'unpaid')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->count();

        // Recent payments
        $recentPayments = Payment::with(['resident.user', 'bill'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Registration records
        $registrations = Registration::with(['resident.user', 'creator'])
            ->whereYear('payment_date', $year)
            ->orderByDesc('payment_date')
            ->get();

        // Expense records
        $expenses = Expense::with('creator')
            ->whereYear('date', $year)
            ->orderByDesc('date')
            ->get();

        $availableYears = Bill::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (!in_array($year, $availableYears)) {
            $availableYears[] = (int) $year;
        }
        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
        }
        rsort($availableYears);

        return view('admin.dashboard', compact(
            'year',
            'totalIncome',
            'totalExpenses',
            'totalRegistrations',
            'currentBalance',
            'monthlyIncome',
            'pendingPayments',
            'totalResidents',
            'unpaidBills',
            'recentPayments',
            'registrations',
            'expenses',
            'availableYears',
        ));
    }
}
