<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LabSoftware extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'lab_id',
        'software_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $table = 'lab_softwares';

    public function lab()
    {
        return $this->belongsTo(Labs::class, 'lab_id', 'id');
    }
    public function software()
    {
        return $this->belongsTo(Software::class, 'software_id', 'id');
    }
}
