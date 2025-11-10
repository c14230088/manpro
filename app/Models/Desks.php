<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Desks extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'location',
        'lab_id',
        // 'serial_code',
        // 'condition',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['overall_condition'];


    public function lab()
    {
        return $this->belongsTo(Labs::class);
    }

    public function items()
    {
        return $this->hasMany(Items::class, 'desk_id');
    }



    public function getOverallConditionAttribute()
    {

        $this->loadMissing('items.components');

        if ($this->items->isEmpty()) {
            return 'item_kosong';
        }

        foreach ($this->items as $item) {
            if ($item->condition == 0) return 'item_rusak';

            foreach ($item->components as $component) {
                if ($component->condition == 0) return 'component_rusak';
            }
        }

        $requiredTypeNames = ['KEYBOARD', 'CPU', 'MONITOR', 'MOUSE'];

        $presentTypeNames = $this->items
            ->pluck('type.name')
            ->map(fn($name) => strtoupper($name))
            ->unique()
            ->toArray();

        foreach ($requiredTypeNames as $name) {
            if (!in_array($name, $presentTypeNames)) {
                return 'item_tidak_lengkap';
            }
        }

        return 'bagus';
    }
}
