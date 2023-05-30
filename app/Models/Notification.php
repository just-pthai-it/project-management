<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    const USER_ATTACHED_FILES_NOTIFICATION_CONTENT = ':user_name đã đính kèm tệp cho :object :object_name.';
    const USER_SUBMITTED_REPORT_NOTIFICATION_CONTENT = ':user_name đã nộp một báo cáo cho :object :object_name.';

    public const UPDATED_AT = null;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'content',
        'action',
        'created_at',
        'deleted_at',
    ];

    protected $appends = [
        'created_at_for_human',
    ];

    protected function CreatedAtForHuman () : Attribute
    {
        return Attribute::make(
            get: function ()
            {
                Carbon::setLocale('vi');
                return $this->created_at->diffForHumans();
            }
        );
    }

    public function users () : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')->withPivot(['id', 'read_at']);
    }

    public function notifiable () : MorphTo
    {
        return $this->morphTo();
    }
}
