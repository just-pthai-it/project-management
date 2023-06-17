<?php

namespace App\Jobs;

use App\Events\NotificationCreatedEvent;
use App\Mail\CloseToTheDeadline;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyBehindScheduleProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct ()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle ()
    {
        Project::query()->where('status_id', '=', ProjectStatus::STATUS_BEHIND_SCHEDULE)
               ->chunkById(50, function ($projects)
               {
                   foreach ($projects as $project)
                   {
                       $diffForHumans           = now('+7')->diffForHumans($project->created_at);
                       $notificationContent     = __('notification.behind_schedule',
                                                     ['object_type' => 'dự án',
                                                      'object_name' => $project->name,
                                                      'diff'        => $diffForHumans]);
                       $notificationContent     = str_replace(' sau', '', $notificationContent);
                       $NotificationReceiverIds = [$project->user_id];
                       $notification            = $this->__storeNotification($project,
                                                                             ['content' => $notificationContent],
                                                                             $NotificationReceiverIds);
                       $this->__broadcastNotification($notification, $NotificationReceiverIds);
                       $this->__mailToTheProjectOwner($notification, $project->user);
                   }
               });
    }


    private function __storeNotification (Project $project, array $data, array $userIds) : Notification
    {
        $notification = $project->notification()->create($data);
        $notification->users()->attach($userIds);
        return $notification;
    }

    private function __broadcastNotification (Notification $notification, array $userIds) : void
    {
        event(new NotificationCreatedEvent($notification, $userIds));
    }

    private function __mailToTheProjectOwner (Notification $notification, User $owner) : void
    {
        Mail::to($owner)->send(new CloseToTheDeadline($notification));
    }
}
