<?php

namespace App\Services\Contracts;

use App\Models\Notification;

interface NotificationServiceContract
{
    public function list (array $inputs = []);

    public function marksAsRead (Notification $notification);

    public function marksAllAsRead ();
}
