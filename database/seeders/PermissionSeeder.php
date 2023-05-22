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
            'Vai trò'    => [
                ['name'    => 'Xem nhiều',
                 'ability' => 'role:view-any'],
                ['name'    => 'Xem chi tiết',
                 'ability' => 'role:view'],
                ['name'    => 'Thêm',
                 'ability' => 'role:create'],
                ['name'    => 'Sửa',
                 'ability' => 'role:update'],
                ['name'    => 'Xóa',
                 'ability' => 'role:delete'],
            ],
            'Người dùng' => [
                ['name'    => 'Xem nhiều',
                 'ability' => 'user:view-any'],
                ['name'    => 'Xem chi tiết',
                 'ability' => 'user:view'],
                ['name'    => 'Thêm',
                 'ability' => 'user:create'],
                ['name'    => 'Sửa',
                 'ability' => 'user:update'],
                ['name'    => 'Xóa',
                 'ability' => 'user:delete'],
            ],
            'Dự án'      => [
                ['name'    => 'Xem nhiều',
                 'ability' => 'project:view-any'],
                ['name'    => 'Xem chi tiết',
                 'ability' => 'project:view'],
                ['name'    => 'Thêm',
                 'ability' => 'project:create'],
                ['name'    => 'Sửa',
                 'ability' => 'project:update'],
                ['name'    => 'Xóa',
                 'ability' => 'project:delete'],
            ],
            'Đầu việc'   => [
                ['name'    => 'Xem nhiều',
                 'ability' => 'task:view-any'],
                ['name'    => 'Xem chi tiết',
                 'ability' => 'task:view'],
                ['name'    => 'Thêm',
                 'ability' => 'task:create'],
                ['name'    => 'Sửa',
                 'ability' => 'task:update'],
                ['name'    => 'Xóa',
                 'ability' => 'task:delete'],
                ['name'    => 'Báo cáo',
                 'ability' => 'task:report'],
            ],
            'Thống kê'   => [
                ['name'    => 'Thống kê dự án',
                 'ability' => 'statistical:project'],
                ['name'    => 'Thống kê đầu việc',
                 'ability' => 'statistical:task'],
            ],
        ];

        foreach ($permissionGroups as $groupName => $permissionGroup)
        {
            foreach ($permissionGroup as $permission)
            {
                Permission::query()->create(['group_name' => $groupName] + $permission);
            }
        }
    }
}
