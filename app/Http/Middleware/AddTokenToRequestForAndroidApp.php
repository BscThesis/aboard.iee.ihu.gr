<?php

namespace App\Http\Middleware;

use Closure;

class AddTokenToRequestForAndroidApp
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
        \Log::info('before');
        if ($request->exists('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->input('access_token'), true);
            \Log::info('added header');
        }
        \Log::info('after');
        return $next($request);
    }
}
