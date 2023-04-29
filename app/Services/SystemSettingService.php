<?php

namespace App\Services;

use App\Repositories\Contracts\SystemSettingRepositoryContract;

class SystemSettingService implements Contracts\SystemSettingServiceContract
{
    private SystemSettingRepositoryContract $systemSettingRepository;

    public function __construct (SystemSettingRepositoryContract $systemSettingRepository)
    {
        $this->systemSettingRepository = $systemSettingRepository;
    }

    public function list (array $inputs = [])
    {

    }

    public function get (int|string $id, array $inputs = [])
    {

    }

    public function store (array $inputs)
    {

    }

    public function update (int|string $id, array $inputs)
    {

    }

    public function delete (int|string $id)
    {

    }
}
