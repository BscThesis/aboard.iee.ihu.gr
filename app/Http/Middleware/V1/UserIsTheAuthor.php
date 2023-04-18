<?php

namespace App\Http\Middleware\V1;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Announcement;
use App\User;

class UserIsTheAuthor
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
        // get announcement id and user id
        $announcement_id = $request->route('id');
        $user_id = Auth::id();
        $announcement_author = Announcement::select('user_id')->where('id', '=', $announcement_id)->firstOrFail();

        // if the announcements belongs to the said user or the user is an admin, continue
        // else abort with 401 unauthorized
        if ($announcement_author->user_id == $user_id || Auth::user()->is_admin) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}