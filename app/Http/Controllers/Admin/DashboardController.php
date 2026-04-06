<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Resident;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\DatabaseHelper;

class DashboardController extends Controller
{
    public function index(Request $request, FinancialReportService $reportService)
    {
        $year = (int) $request->input('year', now()->year);

        // Fix 11: Use shared service for financial summary
        $summary = Cache::remember("admin_dashboard_summary_{$year}", 300, function () use ($reportService, $year) {
            return $reportService->getSummary($year);
        });

        $totalIncome        = $summary['total_income'];
        $totalRegistrations = $summary['total_registrations'];
        $totalExpenses      = $summary['total_expenses'];
        $currentBalance     = $summary['current_balance'];

        // Fix 11: Monthly breakdown via service
        $monthlyIncomeRaw = $reportService->getMonthlyBreakdown($year);
        $monthlyIncome    = array_map(fn($m) => $m['income'], $monthlyIncomeRaw);

        // Pending payments count
        // Stats with short-lived cache
        $pendingPayments = Cache::remember('pending_payments_count', 60, fn() => Payment::where('status', 'pending')->count());
        $totalResidents  = Cache::remember('total_residents_count', 600, fn() => Resident::where('is_active', true)->count());
        $unpaidBills     = Cache::remember('unpaid_bills_this_month', 60, fn() => 
            Bill::where('status', 'unpaid')->where('month', now()->month)->where('year', now()->year)->count()
        );

        // Recent payments
        $recentPayments = Payment::with(['resident', 'bill'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Registration records
        $registrations = Registration::with(['resident', 'creator'])
            ->whereYear('payment_date', $year)
            ->orderByDesc('payment_date')
            ->get();

        // Expense records
        $expenses = Expense::with('creator')
            ->whereYear('date', $year)
            ->orderByDesc('date')
            ->get();

        $currentYear = now()->year;
        $availableYears = range($currentYear + 2, $currentYear - 3);

        $monthFn = DatabaseHelper::getMonthFunction('payments.payment_date');

        $matrixRaw = Payment::where('payments.status', \App\Enums\PaymentStatus::Confirmed)
            ->whereYear('payments.payment_date', $year)
            ->join('residents', 'residents.id', '=', 'payments.resident_id')
            ->selectRaw("residents.block_number as house, {$monthFn} as month, SUM(payments.amount_paid) as total")
            ->groupBy('house', 'month')
            ->orderBy('house')
            ->get();

        // Get all active units
        $unitQuery = Resident::where('is_active', true);
        
        if ($blockSearch = $request->input('block_search')) {
            $unitQuery->where('block_number', 'like', "%{$blockSearch}%");
        }
        
        $units = $unitQuery->orderBy('block_number')->pluck('block_number');

        $blockMonthlyIncome = [];
        foreach ($units as $house) {
            $blockMonthlyIncome[$house] = array_fill(1, 12, 0);
        }
        foreach ($matrixRaw as $row) {
            if (isset($blockMonthlyIncome[$row->house])) {
                $blockMonthlyIncome[$row->house][(int)$row->month] = (float)$row->total;
            }
        }

        // Filter by unpaid month (where amount is 0)
        if ($unpaidMonth = (int) $request->input('unpaid_month')) {
            if ($unpaidMonth >= 1 && $unpaidMonth <= 12) {
                $blockMonthlyIncome = array_filter($blockMonthlyIncome, function($months) use ($unpaidMonth) {
                    return $months[$unpaidMonth] <= 0;
                });
            }
        }

        if ($request->ajax() && $request->input('partial') === 'matrix') {
            return view('admin.partials.dashboard_matrix_table', compact('blockMonthlyIncome', 'year'));
        }

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
            'units',
            'blockMonthlyIncome',
        ));
    }
}
