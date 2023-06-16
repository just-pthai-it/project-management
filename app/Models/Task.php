<?php

namespace App\Models;

use App\Models\Traits\HasFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes, HasFilter;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'project_id',
        'starts_at',
        'ends_at',
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

    protected $appends = [
        'can_update',
        'can_delete',
        'can_submit_report',
    ];

    private array $filterable = [
        'id',
        'name',
        'project_id',
        'status_id',
    ];

    protected function startsAt () : Attribute
    {
        return Attribute::make(
            get: function ($value)
            {
                $value = Carbon::parse($value, '+7');
                $value->setTimezone('UTC');
                return $value;
            }
        );
    }

    protected function endsAt () : Attribute
    {
        return Attribute::make(
            get: function ($value)
            {
                $value = Carbon::parse($value, '+7');
                $value->setTimezone('UTC');
                return $value;
            }
        );
    }

    protected function canUpdate () : Attribute
    {
        return Attribute::make(
            get: fn () => auth()->user() != null &&
                          (auth()->user()->tokenCan('*') ||
                           (auth()->user()->tokenCan('task:update') &&
                            ($this->user_id == auth()->id() ||
                             $this->users->contains('id', auth()->id()))))
        );
    }

    protected function canDelete () : Attribute
    {
        return Attribute::make(
            get: fn () => auth()->user() != null &&
                          (auth()->user()->tokenCan('*') ||
                           (auth()->user()->tokenCan('task:delete') && auth()->id() == $this->user_id))
        );
    }

    protected function canSubmitReport () : Attribute
    {
        return Attribute::make(
            get: fn () => auth()->user() != null &&
                          (auth()->user()->tokenCan('task:report') &&
                           $this->users->contains('id', auth()->id()))
        );
    }

    public function filterStartAt (Builder $query, ?string $startAt) : void
    {
        if (!empty($startAt))
        {
            $query->where('starts_at', '>=', $startAt);
        }
    }

    public function filterEndAt (Builder $query, ?string $endAt) : void
    {
        if (!empty($endAt))
        {
            $query->where('ends_at', '<=', $endAt);
        }
    }

    public function filterName (Builder $query, ?string $name) : void
    {
        if (!empty($name))
        {
            $query->where('name', 'like', "%{$name}%");
        }
    }

    public function filterAssignee (Builder $query, ?string $userId) : void
    {
        if (!empty($userId))
        {
            $query->whereRelation('users', 'users.id', '=', $userId);
        }
    }

    public function filterProjectName (Builder $query, ?string $projectName) : void
    {
        if (!empty($projectName))
        {
            $query->whereRelation('project', 'projects.name', 'like', "%{$projectName}%");
        }
    }

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
        return $this->belongsToMany(User::class)->using(TaskUser::class);
    }

    public function files () : MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function parent () : BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    public function children () : HasMany
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }

    public function taskUserPairs () : HasMany
    {
        return $this->hasMany(TaskUser::class);
    }

    public function activityLogs () : MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'objectable')->latest();
    }

    public function comments () : MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function notification () : MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
