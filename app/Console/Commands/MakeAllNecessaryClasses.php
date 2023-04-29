<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MakeAllNecessaryClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:all {name} {migration_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make all necessary classes for entity by calling all artisan commands';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        $objectName    = $this->argument('name');
        $migrationName = $this->argument('migration_name');

        Artisan::call("make:model {$objectName}");
        Artisan::call("make:migration create_{$migrationName}_table");
        Artisan::call("make:repository {$objectName}Repository");
        Artisan::call("make:service {$objectName}Service --model");
        Artisan::call("make:controller {$objectName}Controller --api");

        return CommandAlias::SUCCESS;
    }
}
