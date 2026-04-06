<?php

namespace App\Enums;

enum BillStatus: string
{
    case Unpaid  = 'unpaid';
    case Pending = 'pending';
    case Paid    = 'paid';

    public function label(): string
    {
        return match($this) {
            self::Unpaid  => 'Belum Bayar',
            self::Pending => 'Menunggu Konfirmasi',
            self::Paid    => 'Lunas',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Unpaid  => 'bg-danger',
            self::Pending => 'bg-warning text-dark',
            self::Paid    => 'bg-success',
        };
    }
}
