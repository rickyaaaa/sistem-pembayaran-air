<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $resident_id
 * @property int $month
 * @property int $year
 * @property string $amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $month_name
 * @property-read string $period
 * @property-read string $status_badge
 * @property-read \App\Models\Resident $resident
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 */
class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'month',
        'year',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $months[$this->month] ?? '';
    }

    public function getPeriodAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid' => '<span class="badge bg-success">Lunas</span>',
            'pending' => '<span class="badge bg-warning text-dark">Menunggu</span>',
            'unpaid' => '<span class="badge bg-danger">Belum Bayar</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }
}
