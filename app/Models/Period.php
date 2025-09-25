<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Period extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'academic_year',
        'semester',
        'active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'period_id');
    }
}
