<?php

namespace App\Models;

use App\Enums\RegistrationCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'category',
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
            'category' => RegistrationCategory::class,
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category instanceof RegistrationCategory
            ? $this->category->label()
            : (string) $this->category;
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class)->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
