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
        $emails = [
            'root@gmail.com',
            'intern@gmail.com',
            'employee@gmail.com',
            'coleader@gmail.com',
            'leader@gmail.com',
            'comanager@gmail.com',
            'manager@gmail.com',
            'hrm@gmail.com',
        ];

        $users = User::factory()->count(30)->create();
        $roles = Role::all();

        foreach ($users as $user)
        {
            if (empty($emails))
            {
                break;
            }

            $user->update(['email' => array_shift($emails)]);
            $user->roles()->attach($roles->shift());
        }
    }
}
