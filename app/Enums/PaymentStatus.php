<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Menunggu',
            self::Confirmed => 'Dikonfirmasi',
            self::Rejected  => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending   => 'bg-warning text-dark',
            self::Confirmed => 'bg-success',
            self::Rejected  => 'bg-danger',
        };
    }
}
