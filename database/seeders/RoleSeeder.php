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
                 'statistical:project',
                 'statistical:task',
             ]],
            ['name'        => 'Thực tập sinh',
             'permissions' => [
                 'project:view-any',
                 'project:view',
                 'task:view-any',
                 'task:view',
             ]],
            ['name'        => 'Nhân viên chính thức',
             'permissions' => [
                 'project:view-any',
                 'project:view',
                 'task:view-any',
                 'task:view',
                 'task:report',
             ]],
            ['name'        => 'Phó nhóm',
             'permissions' => [
                 'project:view-any',
                 'project:view',
                 'task:view-any',
                 'task:view',
                 'task:update',
                 'task:report',
             ]],
            ['name'        => 'Truởng nhóm',
             'permissions' => [
                 'project:view-any',
                 'project:view',
                 'task:view-any',
                 'task:view',
                 'task:create',
                 'task:update',
                 'task:delete',
                 'task:report',

             ]],
            ['name'        => 'Phó dự án',
             'permissions' => [
                 'project:view-any',
                 'project:view',
                 'project:update',
                 'task:view-any',
                 'task:view',
                 'task:create',
                 'task:update',
                 'task:delete',
                 'task:report',
             ]],
            ['name'        => 'Trưởng dự án',
             'permissions' => [
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
            ['name'        => 'Quản lý nhân sự',
             'permissions' => [
                 'role:view-any',
                 'user:view-any',
                 'user:view',
                 'user:create',
                 'user:update',
                 'user:delete',
             ]],
            ['name'        => 'Giám sát viên',
             'permissions' => [
                 'statistical:project',
                 'statistical:task',
                 'project:view-any',
                 'project:view',
                 'task:view-any',
                 'task:view',
             ]],
        ];

        foreach ($roles as $role)
        {
            $roleObj     = Role::query()->create(Arr::only($role, ['name']));
            $permissions = Permission::query()->whereIn('ability', $role['permissions'])->get();
            $roleObj->permissions()->attach($permissions);
        }
    }
}
