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
                'role:list',
                'role:create',
                'role:update',
                'role:delete',
            ],
            'user'    => [
                'user_view',
                'user:list',
                'user:create',
                'user:update',
                'user:delete',
                'user:deactivate',
                'user:activate',
            ],
            'project' => [
                'project:view',
                'project:list',
                'project:create',
                'project:update',
                'project:delete',
            ],
            'Task'    => [
                'task:view',
                'task:list',
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
