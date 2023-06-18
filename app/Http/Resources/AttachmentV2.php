<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentV2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'announcement_id' => $this->announcement_id,
            'filename' => $this->filename,
            'filesize' => $this->filesize,
            'mime_type' => $this->mime_type,
            'attachment_url' => env("APP_URL")."/api/v2/announcements/". $this->announcement_id."/attachments/". $this->id."?action=download",
            'attachment_url_view' => env("APP_URL")."/api/v2/announcements/". $this->announcement_id."/attachments/". $this->id
        ];
    }
}
