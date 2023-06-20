<?php

namespace App\Http\Controllers;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;

class BaseController extends Controller
{
    private CommandBus $commandBus;

    /**
     */
    public function dispatchCommand ($command, array $commandMiddlewares = [])
    {
        $this->commandBus = $this->__makeCommandBus($commandMiddlewares);
        return $this->commandBus->handle($command);
    }

    public function __makeCommandBus (array $commandMiddlewares = []) : CommandBus
    {
        $commandHandlerMiddleware = $this->__makeCommandHandlerMiddleware();
        $commandMiddlewares       = array_merge($commandMiddlewares, [$commandHandlerMiddleware]);
        return app(CommandBus::class, ['middleware' => $commandMiddlewares]);
    }

    /**
     */
    private function __makeCommandHandlerMiddleware () : CommandHandlerMiddleware
    {
        return app(CommandHandlerMiddleware::class);
    }
}
