<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'name',
        'version',
        // 'license_key',
        // 'vendor',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $table = 'softwares';
    
    public function labs()
    {
        return $this->belongsToMany(
            Labs::class,
            'lab_softwares',
            'software_id',
            'lab_id'
        );
    }
}
