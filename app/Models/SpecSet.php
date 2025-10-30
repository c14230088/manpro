<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpecSet extends Model
{
    use HasUuids;
    protected $table = 'spec_set';

    protected $fillable = [
        'spec_type_id',
        'display_name',
    ];

    public function specType()
    {
        return $this->belongsTo(SpecType::class);
    }

    public function setValues()
    {
        return $this->hasMany(SpecSetValue::class);
    }

    public function components()
    {
        return $this->hasMany(Components::class);
    }
}
