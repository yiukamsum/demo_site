<?php

namespace App\Http\Requests\IncubatorAdvisory;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $check_list = [
            'title'   => 'required',
            'comment'   => 'required',
            'email' => 'required|email:rfc,dns',
            'name'   => 'required|regex:/^[\pL\s\-]+$/u',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
        ];

        $data = Request::session()->get('incubator_register');

        if(!(isset($data['logo_path']))){
            $check_list['logo'] = 'required';
        }

        return $check_list;
    }
}
