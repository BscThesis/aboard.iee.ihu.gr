<?php

namespace App\Http\Requests\V2;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAnnouncement extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        // return auth('api_v2')->check() && auth('api_v2')->user()->is_author;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'title' => $this->title,
            'body' => $this->body,
            'is_event' => $this->is_event,
            'event_location' => $this->event_location,
            'gmaps' => $this->gmaps,
            'event_start_time' => $this->event_start_time,
            'event_end_time' => $this->event_end_time,
            'has_eng' => $this->has_eng,
            'eng_title' => $this->eng_title,
            'eng_body' => $this->eng_body,
            'is_pinned' => $this->is_pinned,
            'pinned_until' => $this->pinned_until,
            'tags' => $this->tags,
            'attachments_old' => $this->attachments_old,
            'attachments' => $this->attachments,
            'user_id' => $this->user_id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'is_event' => 'boolean|nullable',
            'event_location' => 'sometimes|nullable|required_if:is_event,1',
            'gmaps' => 'sometimes|boolean|nullable',
            'event_start_time' => 'sometimes|before_or_equal:event_end_time|date_format:Y-m-d H:i|nullable',
            'event_end_time' => 'sometimes|date_format:Y-m-d H:i|nullable',
            'has_eng' => 'boolean|nullable|sometimes',
            'eng_title' => 'nullable|required_if:has_eng,1|sometimes',
            'eng_body' => 'nullable|required_if:has_eng,1|sometimes',
            'is_pinned' => 'boolean|nullable',
            'pinned_until' => 'nullable|date_format:Y-m-d H:i|required_if:is_pinned,1|sometimes',
            'tags' => 'array|required',
            'attachments' => 'sometimes|array|nullable',
            'attachments_old' => 'sometimes|array|nullable',
            'user_id' => 'sometimes'
        ];
    }

    public function response(array $errors)
    {
        return response()->json($errors, 422);
    }
} 

