<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run () : void
    {
        $this->call([
                        TaskStatusSeeder::class,
                        ProjectStatusSeeder::class,
                        PermissionSeeder::class,
                        RoleSeeder::class,
                        UserSeeder::class,
                        ProjectSeeder::class,
                    ]);

    }
}
