<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodEditRequest extends FormRequest {

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
            'event_id' => 'required|numeric',
            'caterer' => 'required',
            'address' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'zip' => 'required',
            'strat' => 'required|date|date_format:Y-m-d',
            'end' => 'required|date|date_format:Y-m-d',
            'phone' => 'required',
        ];
    }

}