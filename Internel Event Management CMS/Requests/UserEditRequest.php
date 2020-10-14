<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest {

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
            'user_id' => 'required',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'phone' => 'required',
            'role_id' => 'required',
            'status' => 'required|string',
        ];
    }

}
