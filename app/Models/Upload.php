<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'hashed_name',
        'owner_id',
        'destination_type',
        'destination_id',
        'type',
        'size',
        'path',
        'name',
        'extension',
        'mime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getBasenameAttribute()
    {
        return "{$this->hashed_name}.{$this->extension}";
    }

    public function getFilePathAttribute()
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->basename;
    }
}
