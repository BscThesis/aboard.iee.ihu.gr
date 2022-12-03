<?php

namespace App\Http\Middleware\V1;

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
        if ($request->exists('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->input('access_token'), true);
        }
        return $next($request);
    }
}
