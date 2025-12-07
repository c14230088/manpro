<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Bookings_item extends MorphPivot
{
    use HasUuids;
    protected $table = 'bookings_items';

    protected $fillable = [
        'bookable_type',
        'bookable_id',
        'booking_id',
        'type',
        'returner_id',
        'returned_at',
        'returned_status',
        'returned_detail',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookable()
    {
        return $this->morphTo();
    }
    public function returner()
    {
        return $this->belongsTo(User::class, 'returner_id');
    }
}
