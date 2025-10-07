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
        'serial_code',
        'lab_id',
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


    //  'rusak', 'tidak_lengkap', 'bagus'.

    public function getOverallConditionAttribute()
    {

        $this->loadMissing('items.components');

        foreach ($this->items as $item) {
            if ($item->condition == 0) return 'rusak';

            foreach ($item->components as $component) {
                if ($component->condition == 0) return 'rusak';
            }
        }

        $requiredTypes = [0, 1, 2, 3];
        $presentTypes = $this->items->pluck('type')->all();

        foreach ($requiredTypes as $type) {
            if (!in_array($type, $presentTypes)) {
                return 'tidak_lengkap';
            }
        }

        return 'bagus';
    }
}
