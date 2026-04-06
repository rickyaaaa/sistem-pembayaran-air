<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillStatus;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Resident;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, FinancialReportService $reportService)
    {
        $year = (int) $request->input('year', now()->year);

        // Fix 11: Use shared service
        $summary = $reportService->getSummary($year);

        $totalIncome        = $summary['total_income'];
        $totalRegistrations = $summary['total_registrations'];
        $totalExpenses      = $summary['total_expenses'];
        $endingBalance      = $summary['current_balance'];

        $monthlyData    = $reportService->getMonthlyBreakdown($year);
        $availableYears = $reportService->getAvailableYears();

        // Expense by category
        $expensesByCategory = \App\Models\Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Bill collection stats using enum
        $billStats = [
            'total'   => Bill::where('year', $year)->count(),
            'paid'    => Bill::where('year', $year)->where('status', BillStatus::Paid)->count(),
            'pending' => Bill::where('year', $year)->where('status', BillStatus::Pending)->count(),
            'unpaid'  => Bill::where('year', $year)->where('status', BillStatus::Unpaid)->count(),
        ];
        $billStats['collection_rate'] = $billStats['total'] > 0
            ? round(($billStats['paid'] / $billStats['total']) * 100, 1)
            : 0;

        // Merge bill available years into service years
        $billYears = Bill::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
        $availableYears = array_values(array_unique(array_merge($availableYears, $billYears)));
        rsort($availableYears);

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
