<?php

namespace App\Http\Requests\Project;

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
            'title' => 'required',
            'tag' => 'required',
            'description' => 'required',
            'industry_sector' => 'required',
            'location' => 'required',
            'people_number' => 'required|numeric',
            'required_unit' => 'required',
            'required_price' => 'required|numeric',
            'requirement' => 'required',
            'photo_file' => 'required',
            'pdf_file' => 'required',
            'detail' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'industry_sector' => 'Industry Sector',
            'photo_file' => 'Project Photo',
            'pdf_file' => 'Project PDF',
            'people_number' => 'Number of team member',
        ];
    }
}
