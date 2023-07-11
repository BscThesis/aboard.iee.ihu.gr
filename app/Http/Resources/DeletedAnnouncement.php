<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tag as TagResource;
use App\Http\Resources\AttachmentV2 as AttachmentResource;
use \Carbon\Carbon;

class DeletedAnnouncement extends JsonResource
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
            'deleted_at' => $this->deleted_at->format('Y-m-d H:i'),
        ];
    }
}

