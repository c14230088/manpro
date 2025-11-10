<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Tool_spec extends MorphPivot
{
    use HasUuids;

    protected $fillable = [
        'id',
        'spec_value_id',
        'tool_id',
        'tool_type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function tool(): MorphTo
    {
        return $this->morphTo();
    }
}
