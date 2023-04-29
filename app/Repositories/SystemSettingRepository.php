<?php

namespace App\Repositories;

use App\Models\SystemSetting;

class SystemSettingRepository extends Abstracts\ABaseRepository implements Contracts\SystemSettingRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return SystemSetting::class;
    }
}
