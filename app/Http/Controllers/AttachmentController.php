<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('announcement.attachment.check')->only('show');
    }

    /**
     * Display the specified resource.
     *
     * @param   $at_id
     * @param   $an_id
     * @return  \Illuminate\Http\Response
     */
    public function show($an_id, $at_id, Request $request)
    {
        $announcement = Announcement::findOrFail($an_id);
        $local_ip = $request->session()->get('local_ip', 0);

        // if (!($announcement->hasPublicTags() || Auth::check() || Auth::guard('api')->check()) || $local_ip == 0) {
        //     return response()->json([
        //         'message' => 'Unauthenticated'
        //     ], 401);
        // }

        if ($announcement->hasAttachment($at_id)) {
            $attachment = Attachment::findOrFail($at_id);

            $headers = [
                'Content-Type' => $attachment->mime_type,
                'Content-Length' => $attachment->filesize,
            ];

            if ($request->input('action') == 'download') {
                $headers['Content-Disposition'] = "attachment; filename=" . $attachment->filename;
            }

            return response($attachment->content)->withHeaders($headers);
        } else {
            return response()->json([
                'status' => "not_found",
            ]);
        }
    }
}
