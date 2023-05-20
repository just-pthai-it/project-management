<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthenticationService implements Contracts\AuthenticationServiceContract
{
    public function login (string $email, string $password, ?bool $isRememberMe) : JsonResponse
    {
        if (auth()->attempt(['email' => $email, 'password' => $password, 'status' => User::STATUS_ACTIVE]))
        {
            $responseData = [
                'access_token' => $this->__generateTokenForResponse(auth()->user()->isRoot() ? ['all:crud'] : auth()->user()->permissions),
            ];

            if ($isRememberMe === true)
            {
                $responseData['refresh_token'] = $this->__generateTokenForResponse([], true);
            }

            return response()->json(['data' => $responseData]);
        }

        return CusResponse::failed([], 'Invalid credentials', 401);
    }

    public function refreshToken () : JsonResponse
    {
        return response()->json(['data' => [
            'access_token' => $this->__generateTokenForResponse(auth()->user()->isRoot() ? ['all:crud'] : auth()->user()->permissions),
        ]]);
    }

    private function __generateTokenForResponse (array $permissions, bool $isRefreshToken = false) : array
    {
        if ($isRefreshToken)
        {
            $token = auth()->user()->createToken('refresh_token', $permissions, now()->addMonth());
        }
        else
        {
            $token = auth()->user()->createToken('access_token', $permissions);
        }

        return [
            'token'      => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }
}
