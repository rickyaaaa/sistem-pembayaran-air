<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistrationsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private int $year) {}

    public function query()
    {
        return Registration::with(['resident', 'creator'])
            ->where(function($q) {
                $yearFunc = \App\Helpers\DatabaseHelper::getYearFunction('payment_date');
                $q->whereRaw("{$yearFunc} = ?", [$this->year]);
            })
            ->orderBy('payment_date');
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'No. Blok',
            'Nama',
            'Tanggal',
            'Jumlah (Rp)',
            'Catatan',
            'Dicatat Oleh',
        ];
    }

    public function map($reg): array
    {
        return [
            $reg->category_label,
            $reg->resident ? strtoupper($reg->resident->block_number) : '-',
            $reg->resident->name ?? '-',
            $reg->payment_date->format('d/m/Y'),
            (float) $reg->amount,
            $reg->notes ?? '-',
            $reg->creator->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Pemasukan ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
