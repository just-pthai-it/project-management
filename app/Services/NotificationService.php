<?php

namespace App\Services;

use App\Repositories\Contracts\NotificationRepositoryContract;

class NotificationService implements Contracts\NotificationServiceContract
{
    private NotificationRepositoryContract $notificationRepository;

    public function __construct (NotificationRepositoryContract $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
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
