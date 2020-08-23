<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \Carbon\Carbon;

class Notification extends JsonResource
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
            'data' => $this->data,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'read_at' => empty($this->read_at) ? null : Carbon::parse($this->read_at)->format('Y-m-d H:i'),
        ];
    }
}
