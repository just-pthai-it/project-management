<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskUser extends Pivot
{
    public $incrementing = true;

    protected $fillable = [
        'task_id',
        'user_id',
        'created_at',
        'update_at',
    ];

    public function file () : MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
