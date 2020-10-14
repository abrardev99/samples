<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestuarantRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required' , 'string'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
