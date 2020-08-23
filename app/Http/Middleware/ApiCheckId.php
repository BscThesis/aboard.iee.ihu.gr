<?php

namespace App\Http\Middleware;

use Closure;

class ApiCheckId
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
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        } else {
            return $next($request);
        }
    }
}
