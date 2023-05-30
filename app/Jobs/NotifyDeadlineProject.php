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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotifyDeadlineProject implements ShouldQueue
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
    public function handle () : void
    {
        $tomorrow = Carbon::now('+7')->addDay();
        Project::query()
               ->whereNotIn('status_id', [ProjectStatus::STATUS_BEHIND_SCHEDULE, ProjectStatus::STATUS_COMPLETE])
               ->where('ends_at', '=', $tomorrow)
               ->chunkById(50, function ($projects)
               {
                   foreach ($projects as $project)
                   {
                       $project->load('users:id,name,email');
                       $content = $this->__generateContentForNotification($task);

                       $notification = $this->__storeNotification($projects, ['content' => $content],
                                                                  [$project->user_id]);
                       $this->__broadcastNotification($notification, [$project->user_id]);
                       $this->__mailToTheProjectOwner($notification, $project->user);
                   }
               });
    }

    private function __generateContentForNotification (Project $project) : string
    {
        $data[':object']       = Str::ucfirst(__(Str::lower(class_basename(get_class($project)))));
        $data[':object_name'] = $project->name;
        $data[':time']         = $this->scheduleType == 'daily' ? 1 : 12;
        $data[':time_unit']    = __($this->scheduleType == 'daily' ? 'day' : 'hour');
        return Str::swap($data, Notification::NOTIFY_DEADLINE_NOTIFICATION_CONTENT);
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

    private function __mailToTheProjectOwner (Notification $notification, User $ownerId) : void
    {
        Mail::to($ownerId)->send(new CloseToTheDeadline($notification));
    }
}
