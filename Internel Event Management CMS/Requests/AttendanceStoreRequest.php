<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceStoreRequest extends FormRequest {

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
            'user_id' => 'required|numeric',
            'table_id' => 'required|numeric',
            'seat_id' => 'required|numeric',
            'joining_status' => 'required|numeric',
            'att_date_time' => 'required|date_format:Y-m-d H:i:s',
        ];
    }

}
