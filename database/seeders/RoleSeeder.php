<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () : void
    {
        $roles = [
            ['name'        => Role::ROLE_ROOT_NAME,
             'permissions' => [
                 'role:view-any',
                 'role:view',
                 'role:create',
                 'role:update',
                 'role:delete',
                 'user:view-any',
                 'user:view',
                 'user:create',
                 'user:update',
                 'user:delete',
                 'project:view-any',
                 'project:view',
                 'project:create',
                 'project:update',
                 'project:delete',
                 'task:view-any',
                 'task:view',
                 'task:create',
                 'task:update',
                 'task:delete',
                 'task:report',
             ]],
            ['name'        => 'Intern',
             'permissions' => [
                 'project:view-any',
                 'project-view',
                 'task:view-nay',
                 'task:view',
             ]],
            ['name'        => 'Official employee',
             'permissions' => [
                 'project:view-any',
                 'project-view',
                 'task:view-nay',
                 'task:view',
                 'task:report',
             ]],
            ['name'        => 'Co-leader',
             'permissions' => [
                 'project:view-any',
                 'project-view',
                 'task:view-nay',
                 'task:view',
                 'task:update',
                 'task:report',
             ]],
            ['name'        => 'Leader',
             'permissions' => [
                 'project:view-any',
                 'project-view',
                 'task:view-nay',
                 'task:view',
                 'task:create',
                 'task:update',
                 'task:delete',
                 'task:report',

             ]],
            ['name'        => 'Co-manager',
             'permissions' => [
                 'project:view-any',
                 'project-view',
                 'project:update',
                 'task:view-nay',
                 'task:view',
                 'task:create',
                 'task:update',
                 'task:delete',
                 'task:report',
             ]],
            ['name'        => 'Manager',
             'permissions' => [
                 'project:view-any',
                 'project-view',
                 'project:create',
                 'project:update',
                 'project:delete',
                 'task:view-nay',
                 'task:view',
                 'task:create',
                 'task:update',
                 'task:delete',
                 'task:report',
             ]],
            ['name'        => 'Human resource management',
             'permissions' => [
                 'user:view-any',
                 'user:view',
                 'user:create',
                 'user:update',
                 'user:delete',
             ]],
        ];

        foreach ($roles as $role)
        {
            $roleObj     = Role::query()->create(Arr::only($role, ['name']));
            $permissions = Permission::query()->whereIn('name', $role['permissions'])->get();
            $roleObj->permissions()->attach($permissions);
        }
    }
}
