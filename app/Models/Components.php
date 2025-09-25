<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Components extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'serial_code',
        'condition',
        'additional_information',
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
}
