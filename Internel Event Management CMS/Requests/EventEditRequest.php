<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventEditRequest extends FormRequest {

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
            'event_name' => 'required|string',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'reg_start_date' => 'required|date|date_format:Y-m-d',
            'reg_end_date' => 'required|date|date_format:Y-m-d',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
            'status' => 'required|numeric',
        ];
    }

}
