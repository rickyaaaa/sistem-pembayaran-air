<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'payment_date',
        'amount',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
