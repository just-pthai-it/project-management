<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\CommentService;
use App\Services\Contracts\AuthenticationServiceContract;
use App\Services\Contracts\CommentServiceContract;
use App\Services\Contracts\FileServiceContract;
use App\Services\Contracts\NotificationServiceContract;
use App\Services\Contracts\PermissionServiceContract;
use App\Services\Contracts\ProjectServiceContract;
use App\Services\Contracts\ProjectStatusServiceContract;
use App\Services\Contracts\RoleServiceContract;
use App\Services\Contracts\TaskServiceContract;
use App\Services\Contracts\TaskStatusServiceContract;
use App\Services\Contracts\UserServiceContract;
use App\Services\FileService;
use App\Services\NotificationService;
use App\Services\PermissionService;
use App\Services\ProjectService;
use App\Services\ProjectStatusService;
use App\Services\RoleService;
use App\Services\TaskService;
use App\Services\TaskStatusService;
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
        CommentServiceContract::class        => CommentService::class,
        NotificationServiceContract::class   => NotificationService::class,
        RoleServiceContract::class           => RoleService::class,
        ProjectStatusServiceContract::class  => ProjectStatusService::class,
        TaskStatusServiceContract::class     => TaskStatusService::class,
        PermissionServiceContract::class     => PermissionService::class,
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
