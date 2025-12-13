<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folders extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'parent_id', 'full_path', 'is_root', 'creator_id'];

    protected $casts = ['is_root' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(Folders::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folders::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(Files::class, 'folder_id');
    }

    public function matkul()
    {
        return $this->hasOne(Matkul::class, 'root_folder_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
