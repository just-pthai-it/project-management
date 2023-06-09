<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    const ROLE_ROOT_NAME = 'Root';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function users () : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions () : BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }
}
