<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
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

    /**
     * Update an existing loan.
     *
     * @group Loan Management
     *
     * @urlParam id integer required The ID of the loan. Example: 1
     * @bodyParam amount numeric The loan amount. Example: 6000
     * @bodyParam interest_rate numeric The interest rate (percentage). Example: 6.5
     * @bodyParam duration_months integer The loan duration in months. Example: 24
     *
     * @response 200 {
     *   "message": "Loan updated successfully",
     *   "loan": {
     *     "id": 1,
     *     "amount": 6000,
     *     "interest_rate": 6.5,
     *     "duration_months": 24,
     *     "created_at": "2023-06-01T12:00:00Z",
     *     "updated_at": "2023-06-02T12:00:00Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Loan not found"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "amount": [
     *       "The loan amount must be at least 0."
     *     ],
     *     "interest_rate": [
     *       "The interest rate must be between 0 and 100."
     *     ],
     *     "duration_months": [
     *       "The loan duration must be between 1 and 360 months."
     *     ]
     *   }
     * }
     */

     public function update(UpdateLoanRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        \Log::info('Update method called');
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        $loan->update($request->validated());

        return response()->json([
            'message' => 'Loan updated successfully',
            'loan' => $loan
        ]);
    }

    /**
     * Delete an existing loan.
     *
     * @group Loan Management
     *
     * @urlParam id integer required The ID of the loan to delete. Example: 1
     *
     * @response 200 {
     *   "message": "Loan deleted successfully"
     * }
     *
     * @response 404 {
     *   "message": "Loan not found"
     * }
 */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json(['message' => 'Loan not found'], 404);
        }

        $loan->delete();

        return response()->json(['message' => 'Loan deleted successfully'], 200);
    }
}
