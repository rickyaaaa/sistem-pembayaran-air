<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'original_data',
        'requested_data',
        'reason',
        'status',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'original_data'  => 'array',
        'requested_data' => 'array',
        'reviewed_at'    => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    // Get a human-readable model name for display
    public function getModelNameAttribute(): string
    {
        return match($this->model_type) {
            'App\Models\Bill'         => 'Tagihan',
            'App\Models\Payment'      => 'Pembayaran',
            'App\Models\Expense'      => 'Pengeluaran',
            'App\Models\Registration' => 'Pemasukan',
            default                   => class_basename($this->model_type),
        };
    }
}
