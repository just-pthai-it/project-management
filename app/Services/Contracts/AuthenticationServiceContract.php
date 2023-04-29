<?php

namespace App\Services\Contracts;

interface AuthenticationServiceContract
{
    public function login (string $email, string $password, ?bool $isRememberMe);
}
