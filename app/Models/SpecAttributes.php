<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class SpecAttributes extends Model
{
    use HasUuids;
    protected $table = 'spec_attributes';

    protected $fillable = [
        'id',
        'name',
    ];

    public function specValues()
    {
        return $this->hasMany(SpecSetValue::class);
    }
}
