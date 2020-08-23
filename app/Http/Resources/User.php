<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Tag as TagResource;
use \Carbon\Carbon;

class User extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'uid' => $this->uid,
            'is_admin' => $this->is_admin,
            'is_author' => $this->is_author,
            'subscriptions' => TagResource::collection($this->subscriptions),
            'last_interaction_time' => Carbon::now()->toDateTimeString(),
            'last_login_at' => $this->last_login_at,
        ];
    }
    
}
