<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'type' => ['required' , 'numeric']
        ];
    }

    public function authorize()
    {
        return true;
    }
}