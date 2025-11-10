<?php

namespace App\Models;

use App\Models\Type;
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
}
