<?php

namespace App\Http\Middleware\V1;

use Closure;

class WebCheckId
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
        $id = $request->route('id');
        if (!ctype_digit($id)) {
            abort(400);
        } else {
            return $next($request);
        }
    }
}
