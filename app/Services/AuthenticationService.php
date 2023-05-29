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
            $permissions  = array_merge(auth()->user()->permissions, auth()->user()->isRoot() ? ['*'] : []);
            $responseData = [
                'access_token' => $this->__generateAccessTokenInfoForResponse($permissions),
            ];

            if ($isRememberMe === true)
            {
                $responseData['refresh_token'] = $this->__generateRefreshTokenInfoForResponse();
            }

            return response()->json(['data' => $responseData]);
        }

        return CusResponse::failed([], 'Invalid credentials', 401);
    }

    public function refreshToken () : JsonResponse
    {
        $permissions = array_merge(auth()->user()->permissions, auth()->user()->isRoot() ? ['*'] : []);
        return response()->json(['data' => [
            'access_token' => $this->__generateAccessTokenInfoForResponse($permissions),
        ]]);
    }

    private function __generateRefreshTokenInfoForResponse () : array
    {
        $token = auth()->user()->createToken('refresh_token', [], now()->addMonth());
        return [
            'token'      => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }


    private function __generateAccessTokenInfoForResponse (array $permissions) : array
    {
        $token = auth()->user()->createToken('access_token', $permissions);
        return [
            'token'      => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }
}
