<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Session;
use Closure;

class Authenticate
{
	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if (!empty($request->bearerToken()) && !Auth::guard()->check()){
			try{
				$user = Socialite::driver('iee')->userFromToken($request->bearerToken());
				$user1 = User::where('uid', $user['uid'])->first();
				Auth('web')->login($user1);
				return $next($request);		
			} catch (\GuzzleHttp\Exception\BadResponseException $e) {
				Auth('web')->logout();
				Session::flush();
				return response()->json('Invalid Token', 401);
			}    
		}    
		else if (!$request->expectsJson() && !Auth::guard()->check()) {
			return response()->json('Unauthorized', 401);
		}
        return $next($request);
    }
}
