<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Files extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['folder_id', 'original_name', 'stored_name', 'mime_type', 'size', 'creator_id'];

    public function folder()
    {
        return $this->belongsTo(Folders::class, 'folder_id');
    }

    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'matkul_files')->withTimestamps();
    }

    public function module()
    {
        return $this->hasOne(Module::class, 'file_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
