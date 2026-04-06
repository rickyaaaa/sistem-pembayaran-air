<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $bill_id
 * @property int $resident_id
 * @property \Illuminate\Support\Carbon $payment_date
 * @property string $amount_paid
 * @property string $proof_file
 * @property string|null $payer_name
 * @property string|null $payer_phone
 * @property PaymentStatus $status
 * @property int|null $confirmed_by
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bill $bill
 * @property-read \App\Models\Bill $bill
 * @property-read \App\Models\Resident $resident
 * @property-read \App\Models\User|null $confirmedBy
 */
class Payment extends Model
{
    use HasFactory;

    /** Marker untuk payment yang dicatat manual oleh admin (tidak ada file bukti) */
    public const MANUAL_PROOF = 'manual';

    protected $fillable = [
        'bill_id',
        'resident_id',
        'payment_date',
        'amount_paid',
        'proof_file',
        'payer_name',
        'payer_phone',
        'status',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount_paid'  => 'decimal:2',
            'confirmed_at' => 'datetime',
            'status'       => PaymentStatus::class,
        ];
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class)->withTrashed();
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }


}
