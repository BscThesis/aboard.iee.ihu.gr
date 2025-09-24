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
        $isAdmin  = method_exists($this->resource, 'isAdmin')  ? $this->resource->isAdmin()  : false;
        $isAuthor = method_exists($this->resource, 'isAuthor') ? $this->resource->isAuthor() : false;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_eng' => $this->name_eng,
            'email' => $this->email,
            'uid' => $this->uid,
            'is_admin' => $isAdmin,
            'is_author' => $isAuthor,
            'subscriptions' => TagResource::collection($this->subscriptions),
            'last_interaction_time' => \Carbon\Carbon::now()->toDateTimeString(),
            'last_login_at' => $this->last_login_at,
        ];
    }
}
