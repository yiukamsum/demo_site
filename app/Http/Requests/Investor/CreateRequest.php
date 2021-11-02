<?php

namespace App\Http\Requests\Investor;

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
            'industry' => 'required',
            'orientation' => 'required',
            'investor' => 'required',
        ];

        $data = Request::session()->get('service_register');

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
            'industry' => 'Industry sector',
            'orientation' => 'Orientation',
            'investor' => 'Investor type',
        ];
    }
}
