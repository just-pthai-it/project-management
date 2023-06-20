<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeHandlerInCommandBus extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:command-bus:handler {name}';

    protected $type = 'class';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new handler in command bus';


    protected function getStub () : string
    {
        return base_path('/stubs/handler.stub');
    }

    protected function getDefaultNamespace ($rootNamespace) : string
    {
        $extraNamespace = Str::beforeLast($this->argument('name'), '\\');
        if ($extraNamespace == $this->argument('name'))
        {
            return "{$rootNamespace}\CommandBus\Handlers";
        }

        return "{$rootNamespace}\CommandBus\Handlers\\{$extraNamespace}";
    }
}
