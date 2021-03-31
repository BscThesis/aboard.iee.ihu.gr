<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreTag extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return true;
        return Auth::guard('api')->check() && Auth::guard('api')->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => [
                'required',
                'max:255',
                Rule::unique('tags')->ignore($this->id, 'id')
            ],
            'is_public' => 'boolean',
            'parent_id' => 'nullable'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'title.unique' => 'Title must be unique',
            'is_public.boolean' => 'Only boolean values are acceepted'
        ];
    }
}

