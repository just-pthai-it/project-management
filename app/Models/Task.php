<?php

namespace App\Models;

use App\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory, HasFilter;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'project_id',
        'starts_at',
        'ends_at',
        'duration',
        'status_id',
        'pending_reason',
        'parent_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'status_id' => TaskStatus::STATUS_NOT_START,
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function status () : BelongsTo
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function project () : BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users () : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function files () : MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function preTask () : BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    public function subTasks () : HasMany
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }

//    public function constraintTasks ()
//    {
//        return $this->belongsToMany(Task::class, 'task_constraints', '')
//    }
}
