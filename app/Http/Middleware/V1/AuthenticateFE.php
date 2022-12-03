<?php

namespace App\Http\Middleware\V1;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Session;

class AuthenticateFE extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
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
            } catch (\Throwable $e) {
                Auth('web')->logout();
                Session::flush();
                if ($user == null)
                    return response()->json(['message' => 'User not found! Login to our website first in order to activate your account.'], 404);
                return response()->json(['message' => $e->getMessage()], 401);
            }   
        }
        else if (! $request->expectsJson()) {
            return route('login');
        }
    }
}