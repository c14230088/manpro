<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Desks extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        // 'wall',
        'location',
        'serial_code',
        'condition',
        'lab_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function lab()
    {
        return $this->belongsTo(Labs::class);
    }

    public function item()
    {
        return $this->hasMany(Items::class, 'desk_id');
    }
}
