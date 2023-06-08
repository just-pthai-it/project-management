<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasFilter;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'phone',
        'address',
        'job_title',
        'status',
        'avatar',
        'last_login_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pivot',
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'isEditable',
    ];

    private array $filterable = [
        'id',
        'name',
        'email',
    ];

    protected function isEditable () : Attribute
    {
        return Attribute::make(
            get: function ()
            {
                if (!$this->isRoot())
                {
                    return true;
                }

                return auth()->user()->tokenCan('*');
            }
        );
    }

    public function filterName (Builder $query, string $name) : void
    {
        if (!empty($name))
        {
            $query->where('name', 'like', "%{$name}%");
        }
    }

    public function filterEmail (Builder $query, ?string $email) : void
    {
        if (!empty($email))
        {
            $query->where('email', 'like', "%{$email}%");
        }
    }

    public function isRoot () : bool
    {
        return $this->roles()->where('name', '=', Role::ROLE_ROOT_NAME)->exists();
    }

    protected function permissions () : Attribute
    {
        return Attribute::make(
            get: function ()
            {
                $this->load(['roles:id', 'roles.permissions:id,ability']);
                $permissions = collect();
                foreach ($this->roles as $role)
                {
                    $permissions->push(...$role->permissions->pluck('ability')->all());
                }

                return $permissions->unique()->all();
            }
        );
    }

    protected function password () : Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => bcrypt($value)
        );
    }

    public function projects () : HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function assignedProjects () : BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function tasks () : HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function assignedTasks () : BelongsToMany
    {
        return $this->belongsToMany(Task::class)->withTimestamps();
    }

    public function roles () : BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function notifications () : BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot(['id', 'read_at']);
    }

    public function unreadNotifications () : BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot(['id', 'read_at'])->whereNull('read_at');
    }
}
