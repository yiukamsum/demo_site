<?php

namespace App\Http\Requests\CompanyService;

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
            'name' => 'required',
            'description' => 'required',
            'class' => 'required',
            'charging_method' => 'required',
            'price_unit' => 'required',
            'charging_amount' => 'required',
            'service_photo' => 'required'
        ];
    }
}
