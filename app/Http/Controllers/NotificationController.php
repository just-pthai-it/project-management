<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\Contracts\NotificationServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private NotificationServiceContract $notificationService;

    /**
     * @param NotificationServiceContract $notificationService
     */
    public function __construct (NotificationServiceContract $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index (Request $request) : JsonResponse
    {
        return $this->notificationService->list($request->all());
    }

    public function countUnreadNotifications () : JsonResponse
    {
        return $this->notificationService->countUnreadNotifications();
    }

    public function marksAsRead (Notification $notification) : JsonResponse
    {
        return $this->notificationService->marksAsRead($notification);
    }

    public function marksAllAsRead () : JsonResponse
    {
        return $this->notificationService->marksAllAsRead();
    }
}
