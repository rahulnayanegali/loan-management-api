<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // to allow all users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'duration_months' => 'required|integer|min:1|max:360',
        ];
    }

    public function bodyParameters()
    {
        return [
            'amount' => [
                'description' => 'The loan amount in dollars.',
                'example' => 5000,
            ],
            'interest_rate' => [
                'description' => 'The annual interest rate as a percentage.',
                'example' => 5.5,
            ],
            'duration_months' => [
                'description' => 'The duration of the loan in months.',
                'example' => 12,
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'The loan amount is required.',
            'amount.numeric' => 'The loan amount must be a number.',
            'amount.min' => 'The loan amount must be at least 0.',
            'interest_rate.required' => 'The interest rate is required.',
            'interest_rate.numeric' => 'The interest rate must be a number.',
            'interest_rate.min' => 'The interest rate must be at least 0%.',
            'interest_rate.max' => 'The interest rate cannot exceed 100%.',
            'duration_months.required' => 'The loan duration is required.',
            'duration_months.integer' => 'The loan duration must be a whole number.',
            'duration_months.min' => 'The loan duration must be at least 1 month.',
            'duration_months.max' => 'The loan duration cannot exceed 360 months (30 years).',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
