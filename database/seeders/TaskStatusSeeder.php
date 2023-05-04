<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () : void
    {
        $projectStatuses = [
            [
                'name'         => TaskStatus::STATUSES[TaskStatus::STATUS_NOT_START],
                'color'        => '#A5A5A5',
                'is_permanent' => 1,
            ],
            [
                'name'         => TaskStatus::STATUSES[TaskStatus::STATUS_IN_PROGRESS],
                'color'        => '#61C376',
                'is_permanent' => 1,
            ],
            [
                'name'         => TaskStatus::STATUSES[TaskStatus::STATUS_PENDING],
                'color'        => '#D64041',
                'is_permanent' => 1,
            ],
            [
                'name'         => TaskStatus::STATUSES[TaskStatus::STATUS_BEHIND_SCHEDULE],
                'color'        => '#FD9308',
                'is_permanent' => 1,
            ],
            [
                'name'         => TaskStatus::STATUSES[TaskStatus::STATUS_COMPLETE],
                'color'        => '#C8C003',
                'is_permanent' => 1,
            ],
        ];

        TaskStatus::query()->insert($projectStatuses);
    }
}
