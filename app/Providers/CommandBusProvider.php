<?php

namespace App\Providers;

use App\CommandBus\Handler\Locator\ClassNameLocator;
use Illuminate\Support\ServiceProvider;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Plugins\LockingMiddleware;

class CommandBusProvider extends ServiceProvider
{
    public array $bindings = [
        CommandNameExtractor::class => ClassNameExtractor::class,
        HandlerLocator::class       => ClassNameLocator::class,
        MethodNameInflector::class  => HandleInflector::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register () : void
    {
        $this->app->singleton(LockingMiddleware::class, function ()
        {
            return new LockingMiddleware();
        });

        $this->app->bind(CommandHandlerMiddleware::class, function ()
        {
            return new CommandHandlerMiddleware($this->app->make(CommandNameExtractor::class),
                                                $this->app->make(HandlerLocator::class),
                                                $this->app->make(MethodNameInflector::class));
        });
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
