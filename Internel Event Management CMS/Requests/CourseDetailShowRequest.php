<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseDetailShowRequest extends FormRequest {

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
        ];
    }

    protected function validationData() {
        return array_replace_recursive(
                $this->request->all(), $this->route()->parameters()
        );
    }

}
