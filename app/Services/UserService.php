<?php

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\CusResponse;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class UserService implements Contracts\UserServiceContract
{
    private FileServiceContract $fileService;

    /**
     * @param FileServiceContract $fileService
     */
    public function __construct (FileServiceContract $fileService)
    {
        $this->fileService = $fileService;
    }

    public function list (array $inputs = []) : JsonResponse
    {
        $users = User::query()->with(['roles'])->paginate($inputs['per_page'] ?? Constants::DEFAULT_PER_PAGE);
        return (new UserCollection($users))->response();
    }

    public function search (array $inputs = []) : JsonResponse
    {
        $users = User::query()->get(['id', 'name', 'email']);
        return (new UserCollection($users))->response();
    }

    public function get (User $user) : JsonResponse
    {
        return (new UserResource($user))->response();
    }

    public function store (array $inputs) : JsonResponse
    {
        $user = User::query()->create(Arr::except($inputs, ['role_ids']));
        if (isset($inputs['role_ids']))
        {
            $user->roles()->attach($inputs['role_ids']);
        }

        return (new UserResource($user))->response();
    }

    public function update (User $user, array $inputs) : JsonResponse
    {
        $user->update(Arr::except($inputs, ['role_ids']));
        if (isset($inputs['role_ids']))
        {
            $user->roles()->sync($inputs['role_ids']);
        }

        return CusResponse::successful();
    }

    public function updateAvatar (User $user, UploadedFile $file) : JsonResponse
    {
        $fileInfo = $this->__uploadAvatar($user, $file);
        $user->update(['avatar' => $fileInfo['url']]);
        return CusResponse::successful();
    }

    private function __uploadAvatar (User $user, UploadedFile $file)
    {
        return $this->fileService->putUploadedFileAs($file, (string)$user->id, '/avatars');

    }

    public function delete (User $user) : JsonResponse
    {
        $user->delete();
        return CusResponse::successfulWithNoData();
    }

    public function myProfile () : JsonResponse
    {
        return (new UserResource(auth()->user(), true))->response();
    }
}
