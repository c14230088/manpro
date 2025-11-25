<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Labs extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'location',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function desks()
    {
        return $this->hasMany(Desks::class, 'lab_id');
    }

    public function bookings()
    {
        return $this->morphToMany(
            Booking::class,
            'bookable',
            'bookings_items',
            'bookable_id',
            'booking_id'
        )
            ->withPivot(['id', 'type', 'returner_id', 'returned_at', 'returned_status', 'returned_detail'])
            ->using(Bookings_item::class);
    }
}
