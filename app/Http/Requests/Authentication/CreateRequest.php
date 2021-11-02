<?php

namespace App\Http\Requests\Authentication;

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
            'name_cn'   => 'required',
            'name_en' => 'required',
            'type' => 'required',
            'document_number' => 'required',
            'pp_check' => 'accepted',
            'front_document' => 'required',
            'back_document' => 'required',
            'hold_document' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name_cn' => 'Name in Chinese',
            'name_en' => 'Name in English',
            'type' => 'Authentication Type',
            'document_number' => 'Document No',
            'pp_check' => '<< Privacy Policy & User Agreement >>'
        ];
    }
}
