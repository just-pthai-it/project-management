<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
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
                'name'         => ProjectStatus::STATUSES[ProjectStatus::STATUS_NOT_START],
                'color'        => '#A5A5A5',
                'is_permanent' => 1,
            ],
            [
                'name'         => ProjectStatus::STATUSES[ProjectStatus::STATUS_IN_PROGRESS],
                'color'        => '#61C376',
                'is_permanent' => 1,

            ],
            [
                'name'         => ProjectStatus::STATUSES[ProjectStatus::STATUS_PENDING],
                'color'        => '#D64041',
                'is_permanent' => 1,
            ],
            [
                'name'         => ProjectStatus::STATUSES[ProjectStatus::STATUS_BEHIND_SCHEDULE],
                'color'        => '#FD9308',
                'is_permanent' => 1,
            ],
            [
                'name'         => ProjectStatus::STATUSES[ProjectStatus::STATUS_COMPLETE],
                'color'        => '#C8C003',
                'is_permanent' => 1,
            ],
        ];

        ProjectStatus::query()->insert($projectStatuses);
    }
}
