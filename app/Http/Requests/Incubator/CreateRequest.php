<?php

namespace App\Http\Requests\Incubator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
            'eng_name' => 'required',
            'cn_name' => 'required',
            'industry' => 'required',
            'location' => 'required',
            'profile_form' => 'required',
            'logo' => 'required',
            'banner' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'eng_name' => 'Company Name (English)',
            'cn_name' => 'Company Name (Chinese)',
            'industry' => 'Industry Sector',
            'location' => 'Area',
            'profle_form' => 'Company Profile',
            'banner' => 'Banner',
            'logo' => 'Logo',
        ];
    }
}
