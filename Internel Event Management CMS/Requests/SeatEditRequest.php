<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeatEditRequest extends FormRequest {

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
            'id' => 'required|numeric',
            'table_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'name' => 'required',
            'description' => 'required',
            'status' => 'required|numeric',
        ];
    }

}
