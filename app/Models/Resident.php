<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $block_number
 * @property string $block
 * @property string $house_number
 * @property string|null $phone_number
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bill> $bills
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Registration> $registrations
 */
class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'block_number',
        'block',
        'house_number',
        'phone_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
