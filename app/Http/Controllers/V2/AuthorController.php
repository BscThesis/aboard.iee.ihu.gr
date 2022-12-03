<?php

namespace App\Http\Controllers\V2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\V2\Announcement;

class AuthorController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * This function will be used to make all authorization checks such as 
     * If the user is the author of the announcement in order to make changes.
     * 
     * This function is used by every controller that needs an Announcement model in order to update it
     * @param int $announcement_id {The id of the announcement}
     */
    protected function get_user_announcement(int $announcement_id) {
        // If user is logged in 
        if (!auth('api_v2')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if (!$user = auth('api_v2')->user()) {
            return response()->json(['message' => 'Something went wrong. Can\'t fetch user data. Please logout and login'], 401);
        }
        // Get single announcement
        $announcement = Announcement::find($announcement_id);

        if (!$announcement || ($user->id !== $announcement->user_id && !$user->is_admin)) {
            return response()->json(['message' => 'You can\'t edit this announcement'], 401);
        }

        return $announcement;
    }
}
