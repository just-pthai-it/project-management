<?php

namespace App\Services;

use App\Helpers\CusResponse;
use App\Http\Resources\Notification\NotificationCollection;
use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryContract;
use Illuminate\Http\JsonResponse;

class NotificationService implements Contracts\NotificationServiceContract
{
    private NotificationRepositoryContract $notificationRepository;

    public function __construct (NotificationRepositoryContract $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function list (array $inputs = []) : JsonResponse
    {
        $notifications = auth()->user()->notifications()->latest()->cursorPaginate($inputs['per_page'] ?? 10);
        return (new NotificationCollection($notifications))->response();
    }

    public function marksAsRead (Notification $notification) : JsonResponse
    {
        $notification->users()->updateExistingPivot(auth()->id(), ['read_at' => now()]);
        return CusResponse::successfulWithNoData();
    }

    public function marksAllAsRead () : JsonResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return CusResponse::successfulWithNoData();
    }
}
