<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Auth\MyUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('my-ldap', function($app, array $config) {
            return new MyUserProvider($config['model']);
        });

        Passport::routes(function ($router) {
            $router->forAccessTokens();
        });
        Passport::tokensExpireIn(now()->addHours(1));
        Passport::refreshTokensExpireIn(now()->addHours(1));
        Passport::personalAccessTokensExpireIn(now()->addHours(1));
        Passport::cookie('announcements_token');
    }
}
