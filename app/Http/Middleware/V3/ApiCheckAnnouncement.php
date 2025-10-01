<?php

namespace App\Http\Middleware\V3;

use Closure;
use App\Models\V3\Announcement;
use App\Models\V3\GroupTag;

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
        $id = $request->route('id');

        $announcement = Announcement::with(['tags:id,is_public'])
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$announcement) {
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        }

        $local_ip = $request->session()->get('local_ip', 0);

        $hasPublicTag = $announcement->tags->contains(function ($tag) {
            return (bool) $tag->is_public;
        });

        if ($hasPublicTag || $local_ip == 1) {
            return $next($request);
        }

        if (!auth('api_v3')->check()) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        $viewer = auth('api_v3')->user();

        if (!$viewer) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        if ($viewer->is_author || $viewer->is_admin) {
            return $next($request);
        }

        $announcementTagIds = $announcement->tags->pluck('id')->filter()->unique();

        if ($announcementTagIds->isEmpty()) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        $groupIds = $viewer->groups()->pluck('groups.id');

        $userGroupTagIds = GroupTag::whereIn('group_id', $groupIds)
            ->pluck('tag_id')
            ->filter()
            ->unique();

        $intersects = $announcementTagIds->intersect($userGroupTagIds);

        if ($intersects->isNotEmpty()) {
            return $next($request);
        }

        return response()->json([
            'error' => 'Unauthorized'
        ], 401);
    }
}
