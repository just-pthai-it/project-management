<?php

namespace App\CommandBus\Handler\CommandNameExtractor;

use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;

class ClassNameOnlyExtractor implements CommandNameExtractor
{

    /**
     * @inheritDoc
     */
    public function extract ($command) : string
    {
        return class_basename(get_class($command));
    }
}