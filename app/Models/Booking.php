<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasUuids;

    protected $fillable = [
        'bookable_id',
        'bookable_type',
        'period_id',
        'type',
        'book_detail',

        'borrower_id',
        'borrowed_at',

        'returner_id',
        'return_deadline_at',
        'returned_at',
        'returned_status',
        
        'approved',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'book_detail' => 'array', // Otomatis konversi JSON ke array PHP
    ];

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returner_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
}
