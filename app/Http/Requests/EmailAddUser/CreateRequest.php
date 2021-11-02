<?php

namespace App\Http\Requests\EmailAddUser;

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
            'account' => 'required|unique:member',
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'last_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email:rfc,dns|max:255|unique:member',
            'password'         => 'required|min:8|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/',
            'passwordConfirm' => 'required|same:password',
            'emailCode' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'passwordConfirm' => 'confirm password',
            'emailCode' => 'verification code'
        ];
    }
}
