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
        $dir = Str::beforeLast($this->argument('name'), '\\');
        if ($dir == $this->argument('name'))
        {
            return "{$rootNamespace}\CommandBus\Handlers";
        }

        return "{$rootNamespace}\CommandBus\Handlers\\{$dir}";
    }

    protected function replaceClass ($stub, $name) : array|string
    {
        $stub = parent::replaceClass($stub, $name);

        $namespace = $this->getNamespace($name);
        $commandNamespace = str_replace('Handlers', 'Commands', $namespace) . '\\';
        $class        = str_replace($this->getNamespace($name) . '\\', '', $name);
        $commandClass = str_replace('Handler', 'Command', $class);

        return str_replace(['{{ command_namespace }}', '{{ command_class }}'], [$commandNamespace, $commandClass], $stub);
    }
}
