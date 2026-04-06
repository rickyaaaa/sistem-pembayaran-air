<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private int $year) {}

    public function query()
    {
        return Expense::with('creator')
            ->where(function($q) {
                $yearFunc = \App\Helpers\DatabaseHelper::getYearFunction('date');
                $q->whereRaw("{$yearFunc} = ?", [$this->year]);
            })
            ->orderBy('date');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jumlah (Rp)',
            'Keterangan',
            'Kategori',
            'Dicatat Oleh',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->date->format('d/m/Y'),
            (float) $expense->amount,
            $expense->description,
            $expense->category,
            $expense->creator->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
