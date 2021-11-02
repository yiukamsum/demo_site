<?php

namespace App\Http\Requests\IxcServiceAdvisory;

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
        return [
            'service'   => 'required',
            'comment'   => 'required',
            'email' => 'required|email:rfc,dns',
            'name'   => 'required|regex:/^[\pL\s\-]+$/u',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
        ];
    }
}
