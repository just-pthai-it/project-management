<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;

class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    protected $type = 'class';

    public function handle () : ?bool
    {
        Artisan::call('make:repository-contract', ['name' => "{$this->argument('name')}Contract"]);
        return parent::handle();
    }

    protected function getStub () : string
    {
        return base_path('/stubs/repository.stub');
    }

    protected function getDefaultNamespace ($rootNamespace) : string
    {
        return "{$rootNamespace}\Repositories";
    }

    protected function replaceClass ($stub, $name) : array|string
    {
        $stub  = parent::replaceClass($stub, $name);
        $model = str_replace('Repository', '', $this->argument('name'));
        return str_replace('{{ model }}', $model, $stub);
    }
}
