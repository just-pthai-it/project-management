<?php

namespace App\Providers;

use App\Repositories\Contracts\ProjectRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryContract::class    => UserRepository::class,
        ProjectRepositoryContract::class => ProjectRepository::class,
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
