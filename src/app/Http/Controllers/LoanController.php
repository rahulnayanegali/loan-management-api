<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Models\Loan;

class LoanController extends Controller
{

    /**
     * Create a new loan.
     *
     * @group Loan Management
     *
     * @bodyParam amount numeric required The loan amount. Example: 5000
     * @bodyParam interest_rate numeric required The interest rate (percentage). Example: 5.5
     * @bodyParam duration_months integer required The loan duration in months. Example: 12
     *
     * @response 201 {
     *   "message": "Loan created successfully",
     *   "loan": {
     *     "id": 1,
     *     "amount": 5000,
     *     "interest_rate": 5.5,
     *     "duration_months": 12,
     *     "created_at": "2023-06-01T12:00:00Z",
     *     "updated_at": "2023-06-01T12:00:00Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "amount": [
     *       "The loan amount is required."
     *     ],
     *     "interest_rate": [
     *       "The interest rate is required."
     *     ],
     *     "duration_months": [
     *       "The loan duration is required."
     *     ]
     *   }
     * }
     */
    public function store(StoreLoanRequest $request)
    {
        // Create and save the new loan
        $loan = Loan::create($request->validated());

        // Return success response
        return response()->json([
            'message' => 'Loan created successfully',
            'loan' => $loan
        ], 201);
    }
}
