<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Announcement;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param   $at_id
     * @param   $an_id
     * @return  \Illuminate\Http\Response
     */
    public function show($an_id, $at_id)
    {
        if (Announcement::findOrFail($an_id)->hasAttachment($at_id)) {
            // return response()->json([
            //     'announcement_id' => $an_id,
            //     'attachment_id' => $at_id
            // ]);
            $attachment = Attachment::findOrFail($at_id);
            return response($attachment->content)
                ->withHeaders([
                    'Content-Disposition' => '"attachment; filename="' . $attachment->filename . '"',
                    'Content-Type' => $attachment->mime_type,
                    'Content-Length' => $attachment->filesize,
                ]);
        } else {
            return response()->json([
                'status' => "not_found",
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function edit(Attachment $attachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attachment $attachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attachment $attachment)
    {
        //
    }
}
