<?php

namespace App\Exports;

use App\Models\Bill;
use App\Enums\BillStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BillsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private int $year) {}

    public function query()
    {
        return Bill::where('bills.year', $this->year)
            ->join('residents', 'residents.id', '=', 'bills.resident_id')
            ->select('bills.*', 'residents.block_number as res_block_number', 'residents.name as res_name')
            ->orderBy('residents.block_number')
            ->orderBy('bills.month');
    }

    public function headings(): array
    {
        return [
            'No. Blok',
            'Nama Warga',
            'Bulan',
            'Tahun',
            'Jumlah Tagihan (Rp)',
            'Status',
        ];
    }

    public function map($bill): array
    {
        $months = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];

        return [
        strtoupper($bill->res_block_number ?? '-'),
        $bill->res_name ?? '-',
            $months[$bill->month] ?? $bill->month,
            $bill->year,
            (float) $bill->amount,
            $bill->status->label(),
        ];
    }

    public function title(): string
    {
        return 'Tagihan ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
