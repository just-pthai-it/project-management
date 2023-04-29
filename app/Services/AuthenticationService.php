<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class AuthenticationService implements Contracts\AuthenticationServiceContract
{

    public function login (string $email, string $password, ?bool $isRememberMe) : JsonResponse
    {
        auth()->attempt(['email' => $email, 'password' => $password]);
        $accessToken = auth()->user()->createToken('access_token')->plainTextToken;
        return response()->json(['data' => auth()->user()])
                         ->cookie('access_token', $accessToken, 1, '', '', true, true)
                         ->header('Authorization', "Bearer {$accessToken}");
    }
}
