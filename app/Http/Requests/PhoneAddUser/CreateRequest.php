<?php

namespace App\Http\Requests\PhoneAddUser;

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
            'phone' => 'required',
            'phoneCode' => 'required',
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'required|regex:/^[\pL\s\-]+$/u'
        ];
    }
    public function messages()
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phoneCode' => 'verification code'
        ];
    }
}
