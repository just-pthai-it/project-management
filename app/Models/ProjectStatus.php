<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    use HasFactory;

    const STATUS_NOT_START = 1;
    const STATUS_PENDING = 2;
    const STATUS_PROGRESS = 3;
    const STATUS_BEHIND_SCHEDULE = 4;
    const STATUS_COMPLETE = 5;


    const STATUSES = [
        self::STATUS_NOT_START       => 'Not Started',
        self::STATUS_PROGRESS        => 'In progress',
        self::STATUS_PENDING         => 'Pending',
        self::STATUS_BEHIND_SCHEDULE => 'Behind schedule',
        self::STATUS_COMPLETE        => 'Complete',
    ];
}
