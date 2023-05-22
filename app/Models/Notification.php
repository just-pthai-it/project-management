<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    const USER_COMMENTED_NOTIFICATION_CONTENT = ':user_name replied your comment in :object :object_name.';
    const USER_ASSIGNED_NOTIFICATION_CONTENT = ':user_name assigned you to :object :object_name.';

    public const UPDATED_AT = null;
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'content',
        'action',
        'created_at',
        'deleted_at',
    ];

    public function users () : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')->withPivot(['id', 'read_at']);
    }

    public function notifiable () : MorphTo
    {
        return $this->morphTo();
    }
}
