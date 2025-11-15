<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasUuids;

    protected $fillable = [
        // Set hanya catat "kekeluargaan" item, dan 
        'id',
        'name',
        'producted_at', // antara period_id atau ini
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
