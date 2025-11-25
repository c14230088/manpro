<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Repairs_item extends MorphPivot
{
    use HasUuids;

    public $timestamps = false;
    protected $table = 'repairs_items';

    protected $fillable = [
        'itemable_type',
        'itemable_id',
        'started_at',
        'completed_at',
        'issue_description',
        'status',
        'is_successful',
        'repair_notes',
        'repair_id'
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
