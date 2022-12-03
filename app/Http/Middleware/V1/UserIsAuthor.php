<?php

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserIsAuthor
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
        if (Auth::user()->is_author) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
