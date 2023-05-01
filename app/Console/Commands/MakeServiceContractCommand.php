<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeServiceContractCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service-contract {name} {--model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new service contract class';

    protected $type = 'class';

    protected function getStub () : string
    {
        if ($this->option('model'))
        {
            return base_path('/stubs/service-contract.model.stub');
        }

        return base_path('/stubs/service-contract.stub');
    }

    protected function getDefaultNamespace ($rootNamespace) : string
    {
        return "{$rootNamespace}\Services\Contracts";
    }

    protected function replaceClass ($stub, $name) : array|string
    {
        $stub                 = parent::replaceClass($stub, $name);
        $NameSpaceExplodedArr = explode('/', $this->argument('name'));
        $model                = str_replace('ServiceContract', '', end($NameSpaceExplodedArr));
        $stub                 = str_replace('{{ model }}', $model, $stub);
        return str_replace('{{ lc_model }}', lcfirst($model), $stub);
    }
}
