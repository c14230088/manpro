<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SpecSetValue extends Model
{
    use HasUuids;
    protected $table = 'spec_set_value';
    
    protected $fillable = [
        'id',
        'spec_attributes_id',
        'value',
    ];

    public function tool_spec()
    {
        return $this->belongsTo(Tool_spec::class);
    }

    public function specAttributes()
    {
        return $this->belongsTo(SpecAttributes::class);
    }
}
