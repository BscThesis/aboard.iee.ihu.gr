<?php

namespace App\Listeners\V3;

use App\Events\V3\NewAnnouncementWasCreatedEvent;
use App\User;
use App\Notifications\V3\AnnouncementCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;

class SendNotificationsToSubscribedUsersListener
{
    /**
     * Handle the event.
     *
     * @param  NewAnnouncementWasCreatedEvent  $event
     * @return void
     */
    public function handle(NewAnnouncementWasCreatedEvent $event)
    {
        $announcement_tags = $event->announcement->tags->pluck(['id'])->all();
        $users = User::whereHas('subscriptions', function (Builder $query)  use ($announcement_tags) {
            $query->whereIn('tag_id', $announcement_tags);
        })->get();
        Notification::send($users, new AnnouncementCreated($event->announcement));
    }
}
