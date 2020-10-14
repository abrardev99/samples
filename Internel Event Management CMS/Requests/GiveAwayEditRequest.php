<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GiveAwayEditRequest extends FormRequest {

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
            'give_away_id' => 'required|numeric',
            'event_id' => 'required|numeric',
            'phone' => 'required|numeric',
            'items' => 'required',
            'quantity' => 'required',
            'vendor' => 'required',
            'sizes' => 'required',
            'address' => 'required',
            'state_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'zip' => 'required|numeric',
        ];
    }

}
