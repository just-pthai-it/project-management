<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;

class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new service class';

    protected $type = 'class';

    public function handle () : ?bool
    {
        Artisan::call('make:service-contract',
                      ['name'    => "{$this->argument('name')}Contract",
                       '--model' => $this->option('model')]);

        return parent::handle();
    }

    protected function getStub () : string
    {
        if ($this->option('model'))
        {
            return base_path('/stubs/service.model.stub');
        }

        return base_path('/stubs/service.stub');

    }

    protected function getDefaultNamespace ($rootNamespace) : string
    {
        return "{$rootNamespace}\Services";
    }

    protected function replaceClass ($stub, $name) : array|string
    {
        $stub             = parent::replaceClass($stub, $name);
        $arr_NameExploded = explode('/', $this->argument('name'));
        $model            = str_replace('Service', '', end($arr_NameExploded));
        $stub             = str_replace('{{ model }}', $model, $stub);
        return str_replace('{{ lc_model }}', lcfirst($model), $stub);
    }
}
