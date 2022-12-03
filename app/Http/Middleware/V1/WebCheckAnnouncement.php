<?php

namespace App\Http\Middleware\V1;

use Closure;
use Models\V1\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WebCheckAnnouncement
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

        if (DB::table('announcements')->where('id', $id)->whereNull('deleted_at')->doesntExist()) {            
            abort(404);
        }

        return $next($request);
    }
}
