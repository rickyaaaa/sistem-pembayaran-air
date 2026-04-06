<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BillStatus;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Resident;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use App\Exports\FinancialReportExport;
use App\Exports\ResidentsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request, FinancialReportService $reportService)
    {
        $year = (int) $request->input('year', now()->year);

        // Fetch all summary data via service
        $summary = $reportService->getSummary($year);

        $totalIncome        = $summary['total_income'];
        $totalRegistrations = $summary['total_registrations'];
        $totalExpenses      = $summary['total_expenses'];
        $endingBalance      = $summary['current_balance'];

        $monthlyData    = $reportService->getMonthlyBreakdown($year);
        $availableYears = $reportService->getAvailableYears();

        // Expense by category
        $expensesByCategory = \App\Models\Expense::selectRaw('category, SUM(amount) as total')
            ->where(function($q) use ($year) {
                 $yearFunc = \App\Helpers\DatabaseHelper::getYearFunction('date');
                 $q->whereRaw("{$yearFunc} = ?", [$year]);
            })
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

    public function exportFinancial(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $filename = "Laporan_Keuangan_SAB_{$year}.xlsx";

        return Excel::download(new FinancialReportExport($year), $filename);
    }

    public function exportResidents()
    {
        $filename = 'Data_Warga_SAB_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ResidentsExport(), $filename);
    }
}
