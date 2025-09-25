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

    public function desk()
    {
        return $this->hasMany(Desks::class, 'lab_id');
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
}
