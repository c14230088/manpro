<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'itemable_type',
        'itemable_id',
        'reported_by',
        'issue_description',
        'status',
        'reported_at',
        'started_at',
        'completed_at',
    ];

    public function itemable()
    {
        return $this->morphTo();
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
