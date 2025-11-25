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
        'id',
        'attendee_count',
        'event_name',
        'event_started_at',
        'event_ended_at',

        'thesis_title',
        'supervisor_id',

        'borrowed_at',
        'return_deadline_at',

        'approved',
        'approved_at',
        'approved_by',

        'borrower_id',
        'phone_number',

        'period_id',
        'booking_detail',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }
    public function bookings_items()
    {
        return $this->hasMany(Bookings_item::class);
    }
}
