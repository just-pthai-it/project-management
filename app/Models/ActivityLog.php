<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    const OBJECT_CREATE_LOG_TYPE_ID = 1;
    const OBJECT_UPDATE_LOG_TYPE_ID = 2;
    const COMMENT_LOG_TYPE_ID = 3;
    const OBJECT_CREATE_LOG_DESCRIPTION = ':user_name created :commentable :object_name.';
    const OBJECT_UPDATE_LOG_DESCRIPTION = ':user_name updated :commentable :object_name.';
    const OBJECT_UPDATE_ATTRIBUTE_LOG_DESCRIPTION = ':user_name updated :attribute of :commentable :object_name from :old_value to :new_value.';
    const COMMENT_LOG_DESCRIPTION = ':user_name commented to :commentable :object_name.';
    const OBJECT_RESOURCE_UPDATE_LOG_DESCRIPTION = ':user_name :action :resource :preposition :commentable :object_name.';


    public const UPDATED_AT = null;
    protected $fillable = [
        'objectable_type',
        'objectable_id',
        'name',
        'type_id',
        'description',
        'user_id',
        'comment_id',
        'created_at',
    ];

    public function comment () : BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
