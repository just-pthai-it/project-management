<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use JetBrains\PhpStorm\NoReturn;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    #[NoReturn] public function run () : void
    {
        $users = User::factory()->count(5)->create();

        $user = $users->first();
        $user->update(['email' => 'user@gmail.com']);
        $rootRoleId = Role::query()->where('name', '=', 'root')->first()->id;
        $user->roles()->attach($rootRoleId);
    }
}
