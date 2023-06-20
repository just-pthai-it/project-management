<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MakeCommandAndHandlerInCommandBus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:command-bus {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call artisan command to make command and handler';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        Artisan::call("make:command-bus:command {$this->argument('name')}Command");
        Artisan::call("make:command-bus:handler {$this->argument('name')}Handler");
        return CommandAlias::SUCCESS;
    }
}
