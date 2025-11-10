<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Type extends Model
{
    use HasUuids;
    protected $table = 'types';

    protected $fillable = [
        'id',
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function items()
    {
        return $this->hasMany(Items::class);
    }

    public function components()
    {
        return $this->hasMany(Components::class);
    }
}
