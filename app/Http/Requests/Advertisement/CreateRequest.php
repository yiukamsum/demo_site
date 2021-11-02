<?php

namespace App\Http\Requests\Advertisement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'ad_id' => 'required',
            'title'   => 'required',
            'brief'   => 'required',
            'icon_file'   => 'required',
            'photo_file'   => 'required',
            'url'   => 'required|url',
            'website'   => 'required|url',
            'email' => 'required|email:rfc,dns',
            'contact_name'   => 'required|regex:/^[\pL\s\-]+$/u',
            'contact_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
        ];
    }
    public function messages()
    {
        return [
            'icon_file' => 'Icon',
            'photo_file' => 'Photo',
        ];
    }
}
