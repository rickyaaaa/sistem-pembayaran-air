<?php

namespace App\Enums;

enum RegistrationCategory: string
{
    case Iuran       = 'iuran';
    case Pendaftaran = 'pendaftaran';
    case KasMasuk    = 'kas_masuk';
    case Sumbangan   = 'sumbangan';
    case Lainnya     = 'lainnya';

    public function label(): string
    {
        return match($this) {
            self::Iuran       => 'Iuran Bulanan',
            self::Pendaftaran => 'Pendaftaran',
            self::KasMasuk    => 'Kas Masuk',
            self::Sumbangan   => 'Sumbangan',
            self::Lainnya     => 'Lainnya',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Iuran       => 'bg-primary',
            self::Pendaftaran => 'bg-info text-dark',
            self::KasMasuk    => 'bg-success',
            self::Sumbangan   => 'bg-warning text-dark',
            self::Lainnya     => 'bg-secondary',
        };
    }

    public function requiresResident(): bool
    {
        return in_array($this, [self::Iuran, self::Pendaftaran]);
    }

    public static function labels(): array
    {
        return array_column(
            array_map(fn($case) => ['key' => $case->value, 'label' => $case->label()], self::cases()),
            'label', 'key'
        );
    }
}
