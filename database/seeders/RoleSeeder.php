<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run () : void
    {
        $rootRole    = Role::query()->create(['name' => 'root']);
        $permissions = Permission::query()->pluck('id')->all();
        $rootRole->permissions()->attach($permissions);

        $roles = [
            ['name' => 'Intern',],
            ['name' => 'Official employee'],
            ['name' => 'Co-leader'],
            ['name' => 'Leader'],
            ['name' => 'Co-manager'],
            ['name' => 'Manager'],
            ['name' => 'Human resource management']
        ];

        Role::query()->insert($roles);
    }
}
