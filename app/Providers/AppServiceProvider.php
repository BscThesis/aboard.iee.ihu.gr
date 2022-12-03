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
        $this->socialiteIeeServiceProvider();
        $this->socialiteIeeApiServiceProvider();
    }

    private function socialiteIeeServiceProvider() {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'iee',
            function ($app) use ($socialite) {
                $config = $app['config']['services.iee'];
                return $socialite->buildProvider(\App\Auth\Social\Two\IeeProvider::class, $config);
            }
        );
    }

    private function socialiteIeeApiServiceProvider() {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'iee_api',
            function ($app) use ($socialite) {
                $config = $app['config']['services.iee_api'];
                return $socialite->buildProvider(\App\Auth\Social\Two\IeeApiProvider::class, $config);
            }
        );
    }
}
