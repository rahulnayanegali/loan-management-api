<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'sometimes|numeric|min:0',
            'interest_rate' => 'sometimes|numeric|min:0|max:100',
            'duration_months' => 'sometimes|integer|min:1',
        ];
    }

    public function bodyParameters()
    {
        return [
            'amount' => [
                'description' => 'The loan amount in dollars.',
                'example' => 6000,
            ],
            'interest_rate' => [
                'description' => 'The annual interest rate as a percentage.',
                'example' => 6.5,
            ],
            'duration_months' => [
                'description' => 'The duration of the loan in months.',
                'example' => 24,
            ],
        ];
    }

    public function messages()
    {
        return [
            'amount.min' => 'The amount field must be at least 0.',
            'interest_rate.max' => 'The interest rate must not exceed 100.',
            'duration_months.min' => 'The duration months must be at least 1.',
        ];
    }
}
