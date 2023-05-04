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
    const OBJECT_CREATE_LOG_DESCRIPTION = ':user_name created :object :object_name.';
    const OBJECT_UPDATE_LOG_DESCRIPTION = ':user_name updated :object :object_name.';
    const OBJECT_UPDATE_ATTRIBUTE_LOG_DESCRIPTION = ':user_name updated :attribute of :object :object_name from :old_value to :new_value.';
    const COMMENT_LOG_DESCRIPTION = ':user_name commented to :object :object_name.';
    const OBJECT_RESOURCE_UPDATE_LOG_DESCRIPTION = ':user_name :action :resource :preposition :object :object_name.';


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
