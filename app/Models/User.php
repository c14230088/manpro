<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'email',
        'unit_id',
    ];

    protected $hidden = [
        // 'password',
        'created_at',
        'updated_at',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function borrower()
    {
        return $this->hasMany(Booking::class, 'borrower_id');
    }
    public function returner()
    {
        return $this->hasMany(Booking::class, 'returner_id');
    }
    public function approver()
    {
        return $this->hasMany(Booking::class, 'approved_by');
    }
    public function repairs()
    {
        return $this->hasMany(Repair::class, 'reported_by');
    }
}
