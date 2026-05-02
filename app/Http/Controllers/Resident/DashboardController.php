<?php

namespace App\Http\Controllers\Resident;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\DatabaseHelper;

class DashboardController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $year = (int) $request->input('year', now()->year);

        $stats = Cache::remember("resident_dashboard_stats_{$year}", 300, function () use ($year): array {
            $totalIncome = Payment::where('status', PaymentStatus::Confirmed)
                ->whereYear('payment_date', $year)
                ->sum('amount_paid');

            $totalRegistrations = Registration::whereYear('payment_date', $year)
                ->sum('amount');

            $totalExpenses = Expense::whereYear('date', $year)
                ->sum('amount');

            return [
                'totalIncome' => $totalIncome,
                'totalRegistrations' => $totalRegistrations,
                'totalExpenses' => $totalExpenses,
                'currentBalance' => ($totalIncome + $totalRegistrations) - $totalExpenses,
            ];
        });

        /** @var float $totalIncome */
        $totalIncome = $stats['totalIncome'];
        /** @var float $totalRegistrations */
        $totalRegistrations = $stats['totalRegistrations'];
        /** @var float $totalExpenses */
        $totalExpenses = $stats['totalExpenses'];
        /** @var float $currentBalance */
        $currentBalance = $stats['currentBalance'];

        $recentExpenses = Expense::whereYear('date', $year)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $recentPayments = Payment::with(['resident', 'bill'])
            ->where('status', PaymentStatus::Confirmed)
            ->whereYear('payment_date', $year)
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $currentYear = now()->year;
        $availableYears = range($currentYear + 2, $currentYear - 3);

        $monthFn = DatabaseHelper::getMonthFunction('payments.payment_date');

        /** @var \Illuminate\Database\Eloquent\Collection $matrixRaw */
        $matrixRaw = Payment::where('payments.status', PaymentStatus::Confirmed)
            ->whereYear('payments.payment_date', $year)
            ->join('residents', 'residents.id', '=', 'payments.resident_id')
            ->selectRaw("residents.block_number as house, {$monthFn} as month, SUM(payments.amount_paid) as total")
            ->groupBy('house', 'month')
            ->orderBy('house')
            ->get();

        $unitQuery = Resident::where('is_active', true);
        if ($blockSearch = $request->input('block_search')) {
            $unitQuery->where('block_number', 'like', "%{$blockSearch}%");
        }
        $units = $unitQuery->orderBy('block_number')->pluck('block_number');

        /** @var array<string, array<int, float>> $blockMonthlyIncome */
        $blockMonthlyIncome = [];
        foreach ($units as $house) {
            $blockMonthlyIncome[$house] = array_fill(1, 12, 0);
        }
        foreach ($matrixRaw as $row) {
            if (isset($blockMonthlyIncome[$row->house])) {
                $blockMonthlyIncome[$row->house][(int) $row->month] = (float) $row->total;
            }
        }

        if ($unpaidMonth = (int) $request->input('unpaid_month')) {
            if ($unpaidMonth >= 1 && $unpaidMonth <= 12) {
                $blockMonthlyIncome = array_filter($blockMonthlyIncome, function($months) use ($unpaidMonth) {
                    return $months[$unpaidMonth] <= 0;
                });
            }
        }

        $perPage = 20;
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $items = array_slice($blockMonthlyIncome, $offset, $perPage, true);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($blockMonthlyIncome),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query(), 'fragment' => 'matrix-section']
        );

        if ($request->ajax() && $request->input('partial') === 'matrix') {
            return view('admin.partials.dashboard_matrix_table', ['blockMonthlyIncome' => $paginator, 'year' => $year]);
        }

        return view('resident.dashboard', compact(
            'year',
            'totalIncome',
            'totalRegistrations',
            'totalExpenses',
            'currentBalance',
            'recentExpenses',
            'recentPayments',
            'availableYears',
        ) + ['blockMonthlyIncome' => $paginator]);
    }

    public function exportExpenses(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $month = $request->input('month');

        if ($month) {
            $month = (int) $month;
            $monthName = \Carbon\Carbon::create()->month($month)->translatedFormat('F');
            $filename = "Pengeluaran_SAB_{$monthName}_{$year}.xlsx";
        } else {
            $month = null;
            $filename = "Pengeluaran_SAB_{$year}.xlsx";
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExpensesExport($year, $month), $filename);
    }
}
