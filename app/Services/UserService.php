<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Http\JsonResponse;

class UserService implements Contracts\UserServiceContract
{
    private UserRepositoryContract $userRepository;

    public function __construct (UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function list (array $inputs = [])
    {
        $users = $this->userRepository->paginate($inputs['per_page'] ?? Constants::DEFAULT_PER_PAGE);
        return (new UserCollection($users));
    }

    public function get (int|string $id, array $inputs = [])
    {

    }

    public function store (array $inputs)
    {

    }

    public function update (int|string $id, array $inputs)
    {

    }

    public function delete (int|string $id)
    {

    }

    public function me () : JsonResponse
    {
        return (new UserResource(auth()->user()))->response();
    }
}
