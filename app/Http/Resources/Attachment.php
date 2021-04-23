<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Attachment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'announcement_id' => $this->announcement_id,
            'filename' => $this->filename,
            // 'content' => base64_encode($this->content),
            'filesize' => $this->filesize,
            'mime_type' => $this->mime_type
        ];
    }
}
