<?php

namespace App\Http\Controllers\V2\Attachment;

use App\Http\Controllers\V2\AuthorController;
use App\Models\V2\Attachment;
use App\Models\V2\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends AuthorController
{
    /**
     * Display the specified resource.
     *
     * @param   $attachment_id
     * @param   $announcement_id
     * @return  \Illuminate\Http\Response
     */
    public function show($announcement_id, $attachment_id, Request $request)
    {
        // Find the announcement with an id of $announcement_id
        $announcement = Announcement::findOrFail($announcement_id);

        // If the announcement has attachments get the attachment with an id of $attachment_id
        if ($announcement->hasAttachment($attachment_id)) {
            $attachment = Attachment::findOrFail($attachment_id);

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
            ], 404);
        }
    }

    public function store($announcement_id, Request $request)
    {
        $announcement = $this->get_user_announcement($announcement_id);
        
        if ($request->input('attachments')) {
            $return_data = [];
            foreach ($request->file('attachments') as $order => $attachment) {
                $att = [
                    'announcement_id'  => $announcement->id,
                    'filename'         => $attachment->getClientOriginalName(),
                    'content'          => file_get_contents($attachment),
                    'filesize'         => $attachment->getSize(),
                    'mime_type'        => $attachment->getMimeType()
                ];
                $announcement->attachments()->create($att);

                $return_data[] = new \App\Resources\Attachment($att);
            }

            return response()->json($return_data, 200);
        }


    }

    public function destroy($announcement_id, $attachment_id)
    {

        $announcement = $this->get_user_announcement($announcement_id);

        // If the announcement has attachments get the attachment with an id of $attachment_id
        if ($announcement->hasAttachment($attachment_id)) {
            $attachment = Attachment::findOrFail($attachment_id);
            if ($attachment->delete()) {

                $headers = [
                    'Content-Type' => $attachment->mime_type,
                    'Content-Length' => $attachment->filesize,
                ];

                return response($attachment->content)->withHeaders($headers);
            } else {
                
            }
        } else {
            return response()->json([
                'status' => "not_found",
            ], 404);
        }
    }
}
