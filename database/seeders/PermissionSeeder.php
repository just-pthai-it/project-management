<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () : void
    {
        $permissionGroups = [
            'Role'    => [
                'role:view-any',
                'role:view',
                'role:create',
                'role:update',
                'role:delete',
            ],
            'User'    => [
                'user:view-any',
                'user:view',
                'user:create',
                'user:update',
                'user:delete',
            ],
            'Project' => [
                'project:view-any',
                'project:view',
                'project:create',
                'project:update',
                'project:delete',
            ],
            'Task'    => [
                'task:view-any',
                'task:view',
                'task:create',
                'task:update',
                'task:delete',
                'task:report',
            ],
        ];

        foreach ($permissionGroups as $groupName => $permissionGroup)
        {
            foreach ($permissionGroup as $permission)
            {
                Permission::query()->create(['group_name' => $groupName, 'name' => $permission]);
            }
        }
    }
}
