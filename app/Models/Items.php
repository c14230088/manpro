<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'serial_code',
        'type',
        'condition',
        'additional_information',
        'unit_id',
        'desk_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function desk()
    {
        return $this->belongsTo(Desks::class);
    }

    public function components()
    {
        return $this->hasMany(Components::class, 'item_id');
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

     public function spec()
    {
        return $this->belongsTo(SpecSet::class, 'spec_set_id');
    }
}
