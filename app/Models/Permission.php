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
        'description',
        'url',
        'action',
        'permission_group_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function permission_group()
    {
        return $this->belongsTo(Permission_group::class);
    }

    public function models()
    {
        return $this->hasMany(Model_permission::class);
    }
}
