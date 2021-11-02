<?php

namespace App\Http\Requests\OfflineEvent;

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
            'offline_title' => 'required',
            'offline_subtitle' => 'required',
            'offline_tag' => 'required',
            'offline_date' => 'required',
            'offline_time' => 'required',
            'banner_file' => 'required',
            'poster_file' => 'required',
            'pdf_file' => 'required',
            'company_name' => 'required',
            'company_website' => 'required|url',
            'district' => 'required',
            'city' => 'required',
            'offline_field' => 'required',
            'offline_description' => 'required',
            'contact_name_en' => 'required',
            'contact_name_cn' => 'required|regex:/^[\pL\s\-]+$/u',
            'contact_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'contact_email' => 'required|email:rfc,dns',
        ];
    }
    public function messages()
    {
        return [
            'offline_title' => 'Title',
            'offline_subtitle' => 'Subtitle',
            'offline_tag' => 'Tag',
            'offline_date' => 'Date',
            'offline_time' => 'Time',
            'banner_file' => 'Banner',
            'poster_file' => 'Poster',
            'pdf_file' => 'Plan PDF',
            'company_name' => 'Company Name',
            'company_website' => 'Company Website',
            'district' => 'District',
            'city' => 'City',
            'offline_field' => 'Field',
            'offline_description' => 'Description',
            'incabutor_name' => 'Incubator location',
            'format' => 'Format',
            'target' => 'Target',
            'schedule' => 'Schedule',
            'quota' => 'Quota',
            'help' => 'Help Service',
            'contact_name_en' => 'Contact Name (English)',
            'contact_name_cn' => 'Contact Name (Chinese)',
            'contact_number' => 'Contact Number',
            'contact_email' => 'Contact Email',
            'wechat' => 'Wechat',
            'charged' => 'Charged',
            'exchange_code' => 'Exchange Code',
            'event_fee' => 'Event Fee',
            'ticket_url' => 'Ticket URL',
        ];
    }
}
