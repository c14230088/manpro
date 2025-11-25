<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Permission_group extends Model
{
    use HasUuids;
    protected $table = 'permission_groups';

    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
