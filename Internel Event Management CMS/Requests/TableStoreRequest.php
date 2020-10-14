<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TableStoreRequest extends FormRequest {

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
            'status' => 'required|numeric',
            'tableable_id' => 'required|numeric',
            'tableable_type' => 'required',
      
        ];
    }

}
