<?php

namespace App\CommandBus\Handler\Locator;

use Illuminate\Contracts\Container\BindingResolutionException;
use League\Tactician\Handler\Locator\HandlerLocator;

class ClassNameLocator implements HandlerLocator
{

    /**
     * @inheritDoc
     * @throws BindingResolutionException
     */
    public function getHandlerForCommand ($commandName)
    {
        $handlerName = str_replace(['Command', 'Commands'], ['Handler', 'Handlers'], $commandName);
        $handlerName = str_replace('HandlerBus', 'CommandBus', $handlerName);
        return app()->make($handlerName);
    }
}