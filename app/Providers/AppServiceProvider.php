<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\Contracts\AuthenticationServiceContract;
use App\Services\Contracts\FileServiceContract;
use App\Services\Contracts\ProjectServiceContract;
use App\Services\Contracts\TaskServiceContract;
use App\Services\Contracts\UserServiceContract;
use App\Services\FileService;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        AuthenticationServiceContract::class => AuthenticationService::class,
        UserServiceContract::class           => UserService::class,
        ProjectServiceContract::class        => ProjectService::class,
        FileServiceContract::class           => FileService::class,
        TaskServiceContract::class           => TaskService::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register ()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot ()
    {
        //
    }
}
