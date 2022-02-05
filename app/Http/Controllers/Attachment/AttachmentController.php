<?php

namespace App\Http\Controllers\Attachment;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Announcement;
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
        $this->middleware('android.app')->only('show');
        $this->middleware('announcement.attachment.check')->only('show');
        $this->middleware('api.announcement.attachment.check')->only('show');
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
        // Find the announcement with an id of $an_id
        $announcement = Announcement::findOrFail($an_id);

        // If the announcement has attachments get the attachment with an id of $at_id
        if ($announcement->hasAttachment($at_id)) {
            $attachment = Attachment::findOrFail($at_id);

            $headers = [
                'Content-Type' => $attachment->mime_type,
                'Content-Length' => $attachment->filesize,
            ];

            // If user requests to download it download the attachment
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
