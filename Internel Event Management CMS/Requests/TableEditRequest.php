<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableEditRequest extends FormRequest {

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
            'name' => 'required',
            'description' => 'required',
            'status' => 'required|numeric',
            'tableable_id' => 'required|numeric',
            'tableable_type' => 'required',
            'seat_qty' => 'required|numeric'
        ];
    }

}
