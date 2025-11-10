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
        'type_id',
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
}
