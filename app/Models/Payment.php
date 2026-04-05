<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $bill_id
 * @property int $resident_id
 * @property \Illuminate\Support\Carbon $payment_date
 * @property string $amount_paid
 * @property string $proof_file
 * @property string $status
 * @property int|null $confirmed_by
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $status_badge
 * @property-read \App\Models\Bill $bill
 * @property-read \App\Models\Resident $resident
 * @property-read \App\Models\User|null $confirmedBy
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'resident_id',
        'payment_date',
        'amount_paid',
        'proof_file',
        'status',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount_paid' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => '<span class="badge bg-success">Dikonfirmasi</span>',
            'pending' => '<span class="badge bg-warning text-dark">Menunggu</span>',
            'rejected' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }
}
