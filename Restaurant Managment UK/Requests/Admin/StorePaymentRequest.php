<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'restaurant' => ['required' , Rule::notIn(0)],
            'category' => ['required' , Rule::notIn(0)],
            'amount' => ['required', 'numeric'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
