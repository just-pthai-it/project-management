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
            'role'    => [
                'role:view',
                'role:view-any',
                'role:create',
                'role:update',
                'role:delete',
            ],
            'user'    => [
                'user_view',
                'user:view-any',
                'user:create',
                'user:update',
                'user:delete',
                'user:deactivate',
                'user:activate',
            ],
            'project' => [
                'project:view',
                'project:view-any',
                'project:create',
                'project:update',
                'project:delete',
            ],
            'Task'    => [
                'task:view',
                'task:view-any',
                'task:create',
                'task:update',
                'task:delete',
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
