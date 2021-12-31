<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiCheckAnnouncement
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
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        }

        $local_ip = $request->session()->get('local_ip', 0);

        // get the number of public tags in an announcement
        $announcement = Announcement::withCount(['tags' => function (Builder $query) {
            $query->where('is_public', '=', 1);
        }])->where('id', $id)->get();

        if (($announcement[0]->tags_count > 0 && $local_ip == 0) || Auth::guard('web')->check()) {
            // if we have at least one public tag, continue
            return $next($request);
        } else if ($announcement[0]->tags_count == 0 && $local_ip == 0) {
            // if we don't, 401 unauthorized
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
