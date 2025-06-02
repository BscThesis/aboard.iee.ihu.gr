<?php

namespace App\Listeners\V3;

use Laravel\Passport\Events\AccessTokenCreated;
use App\Activity;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        Activity::create([
            'type' => 'user.login',
            'user_id' => auth('web')->user()
        ]);
    }
}
