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
        \Log::info(var_dump($request));
        if ($request->exists('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->input('access_token'), true);
        }
        return $next($request);
    }
}
