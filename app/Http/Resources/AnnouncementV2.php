<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tag as TagResource;
use App\Http\Resources\AttachmentV2 as AttachmentResource;
use \Carbon\Carbon;

class AnnouncementV2 extends JsonResource
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
            'title' => $this->title,
            'eng_title' => $this->eng_title,
            'body' => $this->body,
            'eng_body' => $this->eng_body,
            'preview' => mb_substr(strip_tags($this->body), 0, 150, 'utf8'),
            'eng_preview' => mb_substr(strip_tags($this->eng_body), 0, 150, 'utf8'),
            'has_eng' => $this->has_eng,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
            'is_pinned' => $this->is_pinned,
            'pinned_until' => empty($this->pinned_until) ? null : Carbon::parse($this->pinned_until)->format('Y-m-d H:i'),
            'is_event' => $this->is_event,
            'event_start_time' => empty($this->event_start_time) ? null : Carbon::parse($this->event_start_time)->format('Y-m-d H:i'),
            'event_end_time' => empty($this->event_end_time) ? null : Carbon::parse($this->event_end_time)->format('Y-m-d H:i'),
            'event_location' => $this->event_location,
            'gmaps' => $this->gmaps,
            'tags' => TagResource::collection($this->tags),
            'attachments' => AttachmentResource::collection($this->attachments),
	    'author' => !is_null($this->user) ? $this->user->only('name', 'id') : [
            'name' => 'undefined',
            'id' => -1
        ],
	    'announcement_url' => env("APP_URL")."/announcements/".$this->id
        ];
    }
}
