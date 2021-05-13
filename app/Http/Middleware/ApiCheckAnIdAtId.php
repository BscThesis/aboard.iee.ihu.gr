<?php

namespace App\Http\Middleware;

use Closure;

class ApiCheckAnIdAtId
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
        $an_id = $request->route('an_id');
        $at_id = $request->route('at_id');
        if (!ctype_digit($an_id) || !ctype_digit($at_id)) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        } else {
            return $next($request);
        }
    }
}
