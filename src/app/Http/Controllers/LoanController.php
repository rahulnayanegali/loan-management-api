<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Http\Resources\LoanResource;

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

      /**
     * Retrieve a list of all loans.
     *
     * @group Loan Management
     *
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer The number of items per page (default is 15). Example: 10
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "amount": 5000,
     *       "interest_rate": 5.5,
     *       "duration_months": 12,
     *       "created_at": "2023-06-01T12:00:00Z",
     *       "updated_at": "2023-06-01T12:00:00Z"
     *     },
     *     {
     *       "id": 2,
     *       "amount": 10000,
     *       "interest_rate": 6.0,
     *       "duration_months": 24,
     *       "created_at": "2023-06-02T12:00:00Z",
     *       "updated_at": "2023-06-02T12:00:00Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://example.com/api/loans?page=1",
     *     "last": "http://example.com/api/loans?page=1",
     *     "prev": null,
     *     "next": null
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 1,
     *     "path": "http://example.com/api/loans",
     *     "per_page": 15,
     *     "to": 2,
     *     "total": 2
     *   }
     * }
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $validated = request()->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $perPage = $validated['per_page'] ?? 15; // Default to 15 if not specified or invalid
        $page = request()->input('page', 1); // Explicitly get the page from the request

        $loans = Loan::paginate($perPage, ['*'], 'page', $page);
        return LoanResource::collection($loans)->response();

    }
}
