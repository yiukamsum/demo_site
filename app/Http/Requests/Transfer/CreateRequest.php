<?php

namespace App\Http\Requests\Transfer;

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
            'company_type' => 'required',
            'company_size' => 'required',
            'industry' => 'required',
            'established' => 'required',
            'website_form' => 'required|url',
            'profile_form' => 'required',
            'contact_name' => 'required|regex:/^[\pL\s\-]+$/u',
            'contact_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            'price_unit' => 'required|numeric',
            'price' => 'required|numeric',
            'profit_price_unit' => 'required|numeric',
            'profit' => 'required|numeric',
            'income_price_unit' => 'required|numeric',
            'income' => 'required|numeric',
            'expenses_price_unit' => 'required|numeric',
            'expenses' => 'required|numeric',
            'investment_return' => 'required|numeric',
            'name' => 'required',
            'location' => 'required',
            'size' => 'required|numeric',
            'type' => 'required',
            'rent_range' => 'required',
            'lift' => 'required|numeric',
            'detail_price_unit' => 'required|numeric',
            'detail_price' => 'required|numeric',
            'detail_price_interval' => 'required',
            'management_price_unit' => 'required|numeric',
            'management_price' => 'required|numeric',
            'management_price_interval' => 'required',
        ];

        $data = Request::session()->get('transfer_register');

        if(!(isset($data['photo1_file']))){
            $check_list['photo4_file'] = 'required';
        }

        if(!(isset($data['photo2_file']))){
            $check_list['photo4_file'] = 'required';
        }

        if(!(isset($data['photo3_file']))){
            $check_list['photo4_file'] = 'required';
        }

        if(!(isset($data['photo4_file']))){
            $check_list['photo4_file'] = 'required';
        }

        return $check_list;
    }

    public function messages()
    {
        return [
            'eng_name' => 'English name',
            'cn_name' => 'Chinese name',
            'industry' => 'Industry sector',
            'company_type' => 'Company Type',
            'company_size' => 'Company Size',
            'established' => 'Established date',
            'website_form' => 'Company website',
            'profile_form' => 'company profire',
            'contact_name' => 'Company Contact Person',
            'contact_number' => 'Company Contact Number',
            'price_unit' => 'Price Unit',
            'price' => 'Price',
            'profit_price_unit' => 'Profit Unit',
            'profit' => 'Profit',
            'income_price_unit' => 'Income Unit',
            'income' => 'Income',
            'expenses_price_unit' => 'Expenses Unit',
            'expenses' => 'Expenses',
            'investment_return' => 'Investment Return',
            'name' => 'Building Name',
            'location' => 'Location',
            'size' => 'Size',
            'type' => 'Type',
            'rent_range' => 'Rent Range',
            'lift' => 'Lift',
            'detail_price_unit' => 'Detail Price Unit',
            'detail_price' => 'Detail Price',
            'detail_price_interval' => 'Detail Price Interval',
            'management_price_unit' => 'Management Fee Unit',
            'management_price' => 'Management Fee',
            'management_price_interval' => 'Management Fee Interval',
            'photo1_file' => 'Photo1',
            'photo2_file' => 'Photo2',
            'photo3_file' => 'Photo3',
            'photo4_file' => 'Photo4',
        ];
    }
}
