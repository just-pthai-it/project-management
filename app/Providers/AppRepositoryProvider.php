<?php

namespace App\Providers;

use App\Repositories\CommentRepository;
use App\Repositories\Contracts\CommentRepositoryContract;
use App\Repositories\Contracts\FileRepositoryContract;
use App\Repositories\Contracts\NotificationRepositoryContract;
use App\Repositories\Contracts\ProjectRepositoryContract;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\FileRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryContract::class         => UserRepository::class,
        ProjectRepositoryContract::class      => ProjectRepository::class,
        FileRepositoryContract::class         => FileRepository::class,
        TaskRepositoryContract::class         => TaskRepository::class,
        CommentRepositoryContract::class      => CommentRepository::class,
        NotificationRepositoryContract::class => NotificationRepository::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register ()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot ()
    {
        //
    }
}
