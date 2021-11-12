<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Announcement;
use App\Observers\AnnouncementObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Announcement::observe(AnnouncementObserver::class);
    }
}
