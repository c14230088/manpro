<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'route',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
