<?php

namespace App\Http\Requests\FreeStartup;

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
        $check_list = [
            'eng_name' => 'required',
            'cn_name' => 'required',
            'financing' => 'required',
            'product' => 'required',
            'industry' => 'required',
            'location' => 'required',
            'established' => 'required',
            'website_form' => 'required|url',
            'profile_form' => 'required',
            'contact_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'contact_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8'
        ];

        $data = Request::session()->get('free_startup_register');

        if(!(isset($data['logo_path']))){
            $check_list['logo'] = 'required';
        }

        return $check_list;
    }

    public function messages()
    {
        return [
            'eng_name' => 'English name',
            'cn_name' => 'Chinese name',
            'financing' => 'Financing stage',
            'product' => 'Product stage',
            'industry' => 'Industry sector',
            'location' => 'Location',
            'established' => 'Established date',
            'website_form' => 'Company website',
            'profile_form' => 'company profire',
            'contact_name' => 'Company Contact Person',
            'contact_number' => 'Company Contact Number'
        ];
    }
}
