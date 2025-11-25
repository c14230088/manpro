<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Components extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'serial_code',
        'condition',
        'produced_at',

        'type_id',
        'unit_id',
        'item_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class);
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
    public function tool_specs()
    {
        return $this->morphMany(Tool_spec::class, 'tool');
    }

    public function specSetValues()
    {
        return $this->morphToMany(
            SpecSetValue::class,
            'tool',
            'tool_specs',
            'tool_id',
            'spec_value_id'
        )
            ->withPivot('id')
            ->withTimestamps()
            ->using(Tool_spec::class);
    }

    public function repairs()
    {
        return $this->morphToMany(
            Repair::class,
            'itemable',
            'repairs_items',
            'itemable_id',
            'repair_id'
        )
            ->withPivot('id')
            ->using(Repairs_item::class);
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
