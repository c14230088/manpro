<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'reported_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function repairs_item()
    {
        return $this->hasMany(Repairs_item::class, 'repair_id');
    }

    public function items()
    {
        return $this->morphedByMany(Items::class, 'itemable', 'repairs_items')
            ->using(Repairs_item::class)
            ->withPivot(['issue_description', 'status', 'is_successful']);
    }

    public function components()
    {
        return $this->morphedByMany(Components::class, 'itemable', 'repairs_items')
            ->using(repairs_item::class)
            ->withPivot(['issue_description', 'status', 'is_successful']);
    }
}
