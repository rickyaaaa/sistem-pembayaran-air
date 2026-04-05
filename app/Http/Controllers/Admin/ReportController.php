<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Total income: confirmed payments
        $totalIncome = Payment::where('status', 'confirmed')
            ->whereYear('payment_date', $year)
            ->sum('amount_paid');

        // Total registration fees
        $totalRegistrations = Registration::whereYear('payment_date', $year)->sum('amount');

        // Total expenses
        $totalExpenses = Expense::whereYear('date', $year)->sum('amount');

        // Ending balance
        $endingBalance = ($totalIncome + $totalRegistrations) - $totalExpenses;

        // Monthly breakdown
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $income = Payment::where('status', 'confirmed')
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('amount_paid');

            $regFees = Registration::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('amount');

            $expense = Expense::whereYear('date', $year)
                ->whereMonth('date', $m)
                ->sum('amount');

            $monthlyData[$m] = [
                'income' => $income,
                'registrations' => $regFees,
                'expenses' => $expense,
                'balance' => ($income + $regFees) - $expense,
            ];
        }

        // Expense by category
        $expensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Bill collection stats
        $billStats = [
            'total' => Bill::where('year', $year)->count(),
            'paid' => Bill::where('year', $year)->where('status', 'paid')->count(),
            'pending' => Bill::where('year', $year)->where('status', 'pending')->count(),
            'unpaid' => Bill::where('year', $year)->where('status', 'unpaid')->count(),
        ];
        $billStats['collection_rate'] = $billStats['total'] > 0
            ? round(($billStats['paid'] / $billStats['total']) * 100, 1)
            : 0;

        $availableYears = Bill::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }

        return view('admin.reports.index', compact(
            'year',
            'totalIncome',
            'totalRegistrations',
            'totalExpenses',
            'endingBalance',
            'monthlyData',
            'expensesByCategory',
            'billStats',
            'availableYears',
        ));
    }
}
