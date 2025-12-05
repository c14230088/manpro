<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;

class Items extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'serial_code',
        'condition',
        'produced_at',

        'set_id',
        'type_id',
        'unit_id',
        'desk_id',

        'lab_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function desk()
    {
        return $this->belongsTo(Desks::class);
    }

    public function lab()
    {
        return $this->belongsTo(Labs::class);
    }

    public function components()
    {
        return $this->hasMany(Components::class, 'item_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
    public function set()
    {
        return $this->belongsTo(Set::class, 'set_id');
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
            ->withPivot(['id', 'started_at', 'completed_at', 'issue_description', 'status', 'is_successful', 'repair_notes'])
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
