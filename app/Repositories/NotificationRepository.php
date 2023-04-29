<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository extends Abstracts\ABaseRepository implements Contracts\NotificationRepositoryContract
{
    /**
     * @inheritDoc
     */
    function model () : string
    {
        return Notification::class;
    }
}
