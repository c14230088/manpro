<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['file_id', 'matkul_id', 'author_id', 'workload_hours', 'last_edited_at', 'last_edited_by', 'active', 'deleted_by'];

    protected $casts = [
        'active' => 'boolean',
        'last_edited_at' => 'datetime'
    ];

    public function file()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }
    public function deletor()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
