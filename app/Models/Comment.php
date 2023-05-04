<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'content',
        'user_id',
        'deep_level',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable () : MorphTo
    {
        return $this->morphTo();
    }

    public function comments () : MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
