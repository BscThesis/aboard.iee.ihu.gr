<?php namespace App\Providers;

use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\SocialiteServiceProvider;

class SocialitePlusServiceProviderApi extends SocialiteServiceProvider {
    public function register() {
        // Register our own custom Socialite Provider
        $this->app->bind('Laravel\Socialite\Contracts\Factory', function ($app) {
            $socialiteManager = new SocialiteManager($app);
 
            $socialiteManager->extend('iee_api', function() use ($socialiteManager) {
                $config = $this->app['config']['services.iee_api'];
 
                return $socialiteManager->buildProvider(
                    'App\Auth\Social\Two\IeeProvider', $config
                );
            });
            return $socialiteManager;
        });
    }
}
