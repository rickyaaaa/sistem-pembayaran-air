<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinancialReportExport implements WithMultipleSheets
{
    public function __construct(private int $year) {}

    public function sheets(): array
    {
        return [
            new BillsExport($this->year),
            new PaymentsExport($this->year),
            new ExpensesExport($this->year),
            new RegistrationsExport($this->year),
        ];
    }
}
