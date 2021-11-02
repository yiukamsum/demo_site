<?php

namespace App\Http\Requests\OnlineEvent;

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
            'online_title' => 'required',
            'online_subtitle' => 'required',
            'online_tag' => 'required',
            'online_date' => 'required',
            'online_time' => 'required',
            'cover_file' => 'required',
            'mainpage_file' => 'required',
            'company_name' => 'required',
            'company_website' => 'required|url',
            'online_field' => 'required',
            'online_description' => 'required',
            'contact_name_en' => 'required',
            'contact_name_cn' => 'required|regex:/^[\pL\s\-]+$/u',
            'contact_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'contact_email' => 'required|email:rfc,dns',
            'stream_url' => 'required|url',
        ];
    }
    public function messages()
    {
        return [
            'online_title' => 'Title',
            'online_subtitle' => 'Subtitle',
            'online_tag' => 'Tag',
            'online_date' => 'Date',
            'online_time' => 'Time',
            'cover_file' => 'Cover Photo',
            'mainpage_file' => 'Mainpage Photo',
            'company_name' => 'Company Name',
            'company_website' => 'Company Website',
            'online_field' => 'Field',
            'online_description' => 'Description',
            'incubator_name' => 'Incubator location',
            'contact_name_en' => 'Contact Name (English)',
            'contact_name_cn' => 'Contact Name (Chinese)',
            'contact_number' => 'Contact Number',
            'contact_email' => 'Contact Email',
            'stream_url' => 'Stream URL',
        ];
    }
}
