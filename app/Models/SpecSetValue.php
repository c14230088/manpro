<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpecSetValue extends Model
{
    use HasUuids;
    protected $table = 'spec_set_value';
    
    protected $fillable = [
        'spec_set_id',
        'spec_attributes_id',
        'value',
    ];
    public function specSet()
    {
        return $this->belongsTo(SpecSet::class);
    }

    /**
     * Get the attribute that this value corresponds to.
     */
    public function specAttributes()
    {
        return $this->belongsTo(SpecAttributes::class);
    }
}
