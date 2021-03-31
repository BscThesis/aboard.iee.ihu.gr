<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class WebCheckTag
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
        // If requested resouces does not exist, abort with 404
        $id = $request->route('id');
        if (DB::table('tags')->where('id', $id)->whereNull('deleted_at')->doesntExist()) {
            abort(404);
        }

        return $next($request);
    }
}
