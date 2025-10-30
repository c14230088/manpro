<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpecType extends Model
{
    use HasUuids;
    protected $table = 'spec_type';

    protected $fillable = [
        'name',
    ];
    public function specSets()
    {
        return $this->hasMany(SpecSet::class);
    }

    public function specAttributes()
    {
        return $this->hasMany(SpecAttributes::class);
    }
}
