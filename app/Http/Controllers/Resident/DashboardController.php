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
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);

        $stats = Cache::remember("resident_dashboard_stats_{$year}", 300, function () use ($year) {
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

        $totalIncome = $stats['totalIncome'];
        $totalRegistrations = $stats['totalRegistrations'];
        $totalExpenses = $stats['totalExpenses'];
        $currentBalance = $stats['currentBalance'];

        $recentExpenses = Expense::whereYear('date', $year)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $recentRegistrations = Registration::with('resident')
            ->whereYear('payment_date', $year)
            ->orderByDesc('payment_date')
            ->limit(5)
            ->get();

        $isSqliteYear = config('database.default') === 'sqlite';
        $yearFn = $isSqliteYear ? "strftime('%Y', payment_date)" : "YEAR(payment_date)";

        $availableYears = Payment::selectRaw("DISTINCT {$yearFn} as year")
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        if (!in_array(now()->year, $availableYears)) {
            $availableYears[] = now()->year;
            rsort($availableYears);
        }

        $monthFn = DatabaseHelper::getMonthFunction('payments.payment_date');

        $matrixRaw = Payment::where('payments.status', PaymentStatus::Confirmed)
            ->whereYear('payments.payment_date', $year)
            ->join('residents', 'residents.id', '=', 'payments.resident_id')
            ->selectRaw("residents.block_number as house, {$monthFn} as month, SUM(payments.amount_paid) as total")
            ->groupBy('house', 'month')
            ->orderBy('house')
            ->get();

        $units = Resident::where('is_active', true)
            ->orderBy('block_number')
            ->pluck('block_number');

        $blockMonthlyIncome = [];
        foreach ($units as $house) {
            $blockMonthlyIncome[$house] = array_fill(1, 12, 0);
        }
        foreach ($matrixRaw as $row) {
            if (isset($blockMonthlyIncome[$row->house])) {
                $blockMonthlyIncome[$row->house][(int) $row->month] = (float) $row->total;
            }
        }

        return view('resident.dashboard', compact(
            'year',
            'totalIncome',
            'totalRegistrations',
            'totalExpenses',
            'currentBalance',
            'recentExpenses',
            'recentRegistrations',
            'availableYears',
            'blockMonthlyIncome',
        ));
    }
}
