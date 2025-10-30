<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class SpecAttributes extends Model
{
    use HasUuids;
    protected $table = 'spec_attributes';

    protected $fillable = [
        'spec_type_id',
        'name',
    ];

    public function specType()
    {
        return $this->belongsTo(SpecType::class);
    }
    public function setValues()
    {
        return $this->hasMany(SpecSetValue::class);
    }
}
