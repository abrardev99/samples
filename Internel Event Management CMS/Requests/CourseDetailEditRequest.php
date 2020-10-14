<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseDetailEditRequest extends FormRequest {

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
            'course_detail_id' => 'required|numeric',
            'course_id' => 'required|numeric',
            'reg_start_date' => 'required|date|date_format:Y-m-d',
            'reg_end_date' => 'required|date|date_format:Y-m-d',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
            'room_number' => 'required|numeric',
            'instructor_name' => 'required',
            'av_needs' => 'required',
            'av_pro' => 'required',
            'details' => 'required',
        ];
    }

}
