<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginPostRequest;
use App\Services\AuthenticationService;
use App\Services\Contracts\AuthenticationServiceContract;

class AuthenticationController extends Controller
{
    private AuthenticationServiceContract $authenticationService;

    /**
     * @param AuthenticationServiceContract $authenticationService
     */
    public function __construct (AuthenticationServiceContract $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function login (LoginPostRequest $request)
    {
        return $this->authenticationService->login($request->email, $request->password, $request->remember_me);
    }

    public function refreshToken ()
    {
        return $this->authenticationService->refreshToken();
    }
}
