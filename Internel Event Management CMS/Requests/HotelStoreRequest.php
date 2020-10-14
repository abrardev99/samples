<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelStoreRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|string',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'start' => 'required|date|date_format:Y-m-d',
            'end' => 'required|date|date_format:Y-m-d',
            'address' => 'required|string',
            'zip' => 'required',
            'phone' => 'required|numeric',
        ];
    }

}
