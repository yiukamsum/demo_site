<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Authentication;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
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
            'talker_name' => 'required',
            'talker_post' => 'required',
            'type' => 'required',
            'description' => 'required',
            'location' => 'required',
            'target' => 'required',
            'url' => 'required|url',
            'tag' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'title' => 'Title',
            'talker_name' => 'Talker_Name',
            'talker_post' => 'Talker_Post',
            'type' => 'Type',
            'description' => 'Description',
            'location' => 'Location',
            'target' => 'Target',
            'url' => 'URL',
            'tag' => 'Tag'
        ];
    }
}
