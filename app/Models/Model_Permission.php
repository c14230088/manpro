<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Model_permission extends MorphPivot
{
    use HasUuids;

    protected $table = 'model_permissions';

    protected $fillable = [
        'id',
        'model_type',
        'model_id',
        'permission_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function models()
    {
        return $this->morphTo();
    }
}
