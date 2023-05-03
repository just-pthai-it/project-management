<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'extension',
        'file_path',
        'url',
        'disk',
        'fileable_type',
        'fileable_id',
    ];

    protected $hidden = [
        'fileable_type',
        'fileable_id',
    ];

    public function fileable () : MorphTo
    {
        return $this->morphTo();
    }
}
