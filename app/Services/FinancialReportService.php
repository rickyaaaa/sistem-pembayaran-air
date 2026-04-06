<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseHelper;

class FinancialReportService
{
    public function getSummary(int $year): array
    {
        $totalIncome = Payment::where('status', PaymentStatus::Confirmed)
            ->where(function($q) use ($year) {
                $func = \App\Helpers\DatabaseHelper::getYearFunction('payment_date');
                $q->whereRaw("{$func} = ?", [$year]);
            })
            ->sum('amount_paid');

        $totalRegistrations = Registration::where(function($q) use ($year) {
                $func = \App\Helpers\DatabaseHelper::getYearFunction('payment_date');
                $q->whereRaw("{$func} = ?", [$year]);
            })
            ->sum('amount');

        $totalExpenses = Expense::where(function($q) use ($year) {
                $func = \App\Helpers\DatabaseHelper::getYearFunction('date');
                $q->whereRaw("{$func} = ?", [$year]);
            })
            ->sum('amount');

        return [
            'total_income'        => (float) $totalIncome,
            'total_registrations' => (float) $totalRegistrations,
            'total_expenses'      => (float) $totalExpenses,
            'current_balance'     => (float) ($totalIncome + $totalRegistrations - $totalExpenses),
        ];
    }

    public function getMonthlyBreakdown(int $year): array
    {
        $monthQuery = DatabaseHelper::getMonthFunction('payment_date');
        $expenseMonthQuery = DatabaseHelper::getMonthFunction('date');

        // Single query per model — groupBy month instead of 12-iteration loop
        $incomeByMonth = Payment::where('status', PaymentStatus::Confirmed)
            ->whereYear('payment_date', $year)
            ->selectRaw("{$monthQuery} as month, SUM(amount_paid) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $regByMonth = Registration::whereYear('payment_date', $year)
            ->selectRaw("{$monthQuery} as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $expenseByMonth = Expense::whereYear('date', $year)
            ->selectRaw("{$expenseMonthQuery} as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $income  = (float) ($incomeByMonth[$m]  ?? 0);
            $reg     = (float) ($regByMonth[$m]     ?? 0);
            $expense = (float) ($expenseByMonth[$m] ?? 0);

            $monthlyData[$m] = [
                'income'        => $income,
                'registrations' => $reg,
                'expenses'      => $expense,
                'balance'       => $income + $reg - $expense,
            ];
        }

        return $monthlyData;
    }

    public function getAvailableYears(): array
    {
        $pYearFunc = \App\Helpers\DatabaseHelper::getYearFunction('payment_date');
        $paymentYears = \App\Models\Payment::selectRaw("DISTINCT {$pYearFunc} as year")
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        $billYears = \App\Models\Bill::selectRaw('DISTINCT year')
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        $eYearFunc = \App\Helpers\DatabaseHelper::getYearFunction('date');
        $expenseYears = \App\Models\Expense::selectRaw("DISTINCT {$eYearFunc} as year")
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        $years = array_values(array_unique(array_merge($paymentYears, $billYears, $expenseYears)));

        if (!in_array(now()->year, $years)) {
            $years[] = now()->year;
        }

        rsort($years);

        return $years;
    }
}
