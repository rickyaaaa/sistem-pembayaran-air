<?php

namespace App\Exports;

use App\Models\Payment;
use App\Enums\PaymentStatus;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private int $year) {}

    public function query()
    {
        return Payment::with(['resident', 'bill', 'confirmedBy'])
            ->where('status', PaymentStatus::Confirmed)
            ->where(function($q) {
                $yearFunc = \App\Helpers\DatabaseHelper::getYearFunction('payment_date');
                $q->whereRaw("{$yearFunc} = ?", [$this->year]);
            })
            ->orderBy('payment_date');
    }

    public function headings(): array
    {
        return [
            'No. Blok',
            'Nama Warga',
            'Periode Tagihan',
            'Tgl. Pembayaran',
            'Jumlah Dibayar (Rp)',
            'Nama Penyetor',
            'No. HP',
            'Dikonfirmasi Oleh',
        ];
    }

    public function map($payment): array
    {
        return [
            strtoupper($payment->resident->block_number ?? '-'),
            $payment->resident->name ?? '-',
            $payment->bill->period ?? '-',
            $payment->payment_date->format('d/m/Y'),
            (float) $payment->amount_paid,
            $payment->payer_name ?? '-',
            $payment->payer_phone ?? '-',
            $payment->confirmedBy->name ?? 'Sistem',
        ];
    }

    public function title(): string
    {
        return 'Pembayaran ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
