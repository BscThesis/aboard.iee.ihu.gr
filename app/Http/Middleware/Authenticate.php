<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
		// Try to log user in if request contains a token otherwise redirect to login route
		if (!empty($request->bearerToken()) && !Auth::guard()->check()){
			try{
				$user = Socialite::driver('iee')->userFromToken($request->bearerToken());
				$user1 = User::where('uid', $user['uid'])->first();
				Auth('web')->login($user1);
				return;			
        	} catch (\GuzzleHttp\Exception\BadResponseException $e) {
		        Auth('web')->logout();
				Session::flush();
				return route('login');
       		}    
		}
		else if (! $request->expectsJson()) {
			return route('login');
			}
		}
}
