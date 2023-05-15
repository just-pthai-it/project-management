<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface UserServiceContract
{
    public function list (array $inputs = []);

    public function search (array $inputs = []);

    public function get (User $user);

    public function store (array $inputs);

    public function update (User $user, array $inputs);

    public function updateAvatar (User $user, UploadedFile $file);

    public function delete (User $user);

    public function myProfile ();
}
