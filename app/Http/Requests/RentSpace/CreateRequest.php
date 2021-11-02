<?php

namespace App\Http\Requests\RentSpace;

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
            'address' => 'required',
            'description' => 'required',
            'location' => 'required',
            'location_lat' => 'required|numeric',
            'location_lng' => 'required|numeric',
            'photo1_file' => 'required',
            'photo2_file' => 'required',
            'photo3_file' => 'required',
            'photo4_file' => 'required',
            'size' => 'required|numeric',
            'type' => 'required',
            'rent_range' => 'required',
            'lift' => 'required|numeric',
            'price_unit' => 'required|numeric',
            'price' => 'required|numeric',
            'price_interval' => 'required',
            'management_price_unit' => 'required|numeric',
            'management_price' => 'required|numeric',
            'management_price_interval' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name' => 'Building Name',
            'address' => 'Address',
            'description' => 'Description',
            'location' => 'Google map Code',
            'location_lat' => 'Lat',
            'location_lng' => 'Lng',
            'photo1_file' => 'Photo1',
            'photo2_file' => 'Photo2',
            'photo3_file' => 'Photo3',
            'photo4_file' => 'Photo4',
            'size' => 'Area Size',
            'type' => 'Building Type',
            'rent_range' => 'Rent Range',
            'lift' => 'Number of Lift',
            'price_unit' => 'Price Unit',
            'price' => 'Price',
            'price_interval' => 'Price Interval',
            'management_price_unit' => 'Management fee unit',
            'management_price' => 'Management Fee',
            'management_price_interval' => 'Management Fee Interval'
        ];
    }
}
