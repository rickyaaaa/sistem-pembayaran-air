<?php

namespace App\Exports;

use App\Models\Resident;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResidentsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function query()
    {
        return Resident::orderBy('block')->orderBy('house_number');
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Blok',
            'Blok',
            'No. Rumah',
            'Nama',
            'No. HP',
            'Status',
        ];
    }

    private int $no = 0;

    public function map($resident): array
    {
        $this->no++;

        return [
            $this->no,
            strtoupper($resident->block_number),
            $resident->block,
            $resident->house_number,
            $resident->name,
            $resident->phone_number ?? '-',
            $resident->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function title(): string
    {
        return 'Data Warga';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
