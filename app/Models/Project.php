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

class Project extends Model
{
    use HasFactory, SoftDeletes, HasFilter;

    protected $fillable = [
        'name',
        'customer_name',
        'code',
        'user_id',
        'summary',
        'starts_at',
        'ends_at',
        'duration',
        'status_id',
        'progress',
        'pending_reason',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'status_id' => ProjectStatus::STATUS_NOT_START,
        'progress'  => 0,
    ];

    protected $casts = [
        'starts_at' => 'datetime:Y-m-d',
        'ends_at'   => 'datetime:Y-m-d',
    ];

    private array $filterable = [
        'id',
        'name',
        'status_id',
    ];

    protected function pendingReason () : Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: function ($value)
            {
                if ($this->status_id != ProjectStatus::STATUS_PENDING)
                {
                    return '';
                }

                return $value;
            }
        );
    }

    protected function startsAtWithTime () : Attribute
    {
        return Attribute::make(
            get: function ()
            {
                $value = $this->starts_at;
                $value->setTime(00, 00, 00);
                return $value;
            }
        );
    }

    protected function endsAtWithTime () : Attribute
    {
        return Attribute::make(
            get: function ()
            {
                $value = $this->ends_at;
                $value->setTime(23, 59, 59);
                return $value;
            }
        );
    }

        protected function startsAt () : Attribute
    {
        return Attribute::make(
            set: function ($value)
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
            set: function ($value)
            {
                $value = Carbon::parse($value, '+7');
                $value->setTimezone('UTC');
                return $value;
            }
        );
    }

    public function filterStartAt (Builder $query, ?string $startAt) : void
    {
        if (empty(!$startAt))
        {
            $query->where('starts_at', '>=', $startAt);
        }
    }

    public function filterEndAt (Builder $query, ?string $endAt) : void
    {
        if (empty(!$endAt))
        {
            $query->where('ends_at', '<=', $endAt);
        }
    }

    public function filterName (Builder $query, ?string $name) : void
    {
        if (empty(!$name))
        {
            $query->where('name', 'like', "%{$name}%");
        }
    }

    public function user () : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users () : BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function status () : BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function tasks () : HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function activityLogs () : MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'objectable')->latest();
    }

    public function notification () : MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
