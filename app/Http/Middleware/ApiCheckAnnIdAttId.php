<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiCheckAnnIdAttId
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

        if (DB::table('announcements')->where('id', $an_id)->whereNull('deleted_at')->doesntExist()) {
            return response()->json([
                'error' => 'Resource not found',
                'm' => base64_encode('Announcement')
            ], 404);
        }

        if (DB::table('attachments')->where('id', $at_id)->whereNull('deleted_at')->doesntExist()) {
            return response()->json([
                'error' => 'Resource not found',
                'm' => base64_encode('Attachment')
            ], 404);
        }

        $local_ip = $request->session()->get('local_ip', 0);

        $announcement = Announcement::findOrFail($an_id);

        if (!$announcement->hasAttachment($at_id)) {
            return response()->json([
                'error' => 'Unprocessable Entity'
            ], 422);
        }

        if (($announcement->hasPublicTags() && $local_ip == 0) || Auth::guard('api')->check() || Auth::check()) {
            return $next($request);
        } else if (!$announcement->hasPublicTags() && $local_ip == 0) {
            return response()->json([
                'message' => 'Unauthorized 1'
            ], 401);
        }

        return $next($request);
    }
}
