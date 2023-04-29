<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRepositoryContractCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository-contract {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository contract class';

    protected $type = 'class';

    protected function getStub () : string
    {
        return base_path('/stubs/repository-contract.stub');
    }


    protected function getDefaultNamespace ($rootNamespace) : string
    {
        return "{$rootNamespace}\Repositories\Contracts";
    }

    protected function replaceClass ($stub, $name) : array|string
    {
        $stub  = parent::replaceClass($stub, $name);
        $model = str_replace('RepositoryContract', '', $this->argument('name'));
        return str_replace('{{ model }}', $model, $stub);
    }
}
