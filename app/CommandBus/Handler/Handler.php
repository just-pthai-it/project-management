<?php

namespace App\CommandBus\Handler;

interface Handler
{
    public function handle ($command);
}