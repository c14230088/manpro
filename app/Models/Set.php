<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasUuids;

    protected $fillable = [
        // Set hanya catat "kekeluargaan" item
        'id',
        'name',
        'note',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function items()
    {
        return $this->hasMany(Items::class);
    }
}
