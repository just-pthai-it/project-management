<?php

namespace App\CommandBus\Commands\Role;

class GetListRolesCommand
{
    private array $input;

    /**
     * @return array
     */
    public function getInput () : array
    {
        return $this->input;
    }

    /**
     * @param array $input
     */
    public function __construct (array $input = [])
    {
        $this->input = $input;
    }
}