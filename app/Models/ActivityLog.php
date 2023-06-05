<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    const OBJECT_CREATE_LOG_TYPE_ID = 1;
    const OBJECT_UPDATE_LOG_TYPE_ID = 2;

    public const UPDATED_AT = null;

    protected $fillable = [
        'objectable_type',
        'objectable_id',
        'name',
        'type_id',
        'description',
        'user_id',
        'created_at',
    ];

    public function objectable () : MorphTo
    {
        return $this->morphTo();
    }

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
