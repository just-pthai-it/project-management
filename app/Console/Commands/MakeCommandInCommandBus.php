<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeCommandInCommandBus extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:command-bus:command {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new command in command bus';

    protected $type = 'class';

    protected function getStub () : string
    {
        return base_path('/stubs/command.stub');
    }

    protected function getDefaultNamespace ($rootNamespace) : string
    {
        $extraNamespace = Str::beforeLast($this->argument('name'), '\\');
        if ($extraNamespace == $this->argument('name'))
        {
            return "{$rootNamespace}\CommandBus\Commands";
        }

        return "{$rootNamespace}\CommandBus\Commands\\{$extraNamespace}";
    }
}
