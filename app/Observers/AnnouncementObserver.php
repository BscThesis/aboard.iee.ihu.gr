<?php

namespace App\Observers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AnnouncementCreated;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementObserver
{
    /**
     * Handle the announcement "saved" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function saved(Announcement $announcement)
    {
        \Log::info("observer hit");
        // $users = User::where('is_author', 0)->where('is_admin', 0)->get();
        // Notification::send($users, new AnnouncementCreated($announcement));
    }

    /**
     * Handle the announcement "updated" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function updated(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the announcement "deleted" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function deleted(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the announcement "restored" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function restored(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the announcement "force deleted" event.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return void
     */
    public function forceDeleted(Announcement $announcement)
    {
        //
    }
}
