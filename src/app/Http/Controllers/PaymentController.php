<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Add a new payment to a loan.
     *
     * @group Payment Management
     *
     * @urlParam loanId integer required The ID of the loan. Example: 1
     * @bodyParam amount numeric required The payment amount. Example: 500.00
     *
     * @response 201 {
     *   "message": "Payment added successfully",
     *   "payment": {
     *     "id": 1,
     *     "loan_id": 1,
     *     "amount": 500.00,
     *     "created_at": "2023-06-01T12:00:00Z",
     *     "updated_at": "2023-06-01T12:00:00Z"
     *   },
     *   "remaining_balance": 4500.00
     * }
     *
     * @response 404 {
     *   "message": "Loan not found."
     * }
     *
     * @response 422 {
     *   "message": "Payment amount exceeds the remaining loan balance."
     * }
     *
     * @param StorePaymentRequest $request
     * @param int $loanId
     * @return JsonResponse
     */
    public function store(StorePaymentRequest $request, $loanId): JsonResponse
    {
        $loan = Loan::find($loanId);

        if (!$loan) {
            return response()->json([
                'message' => 'Loan not found.',
            ], 404);
        }

        $validatedData = $request->validated();

        if ($loan->remaining_balance < $validatedData['amount']) {
            return response()->json([
                'message' => 'Payment amount exceeds the remaining loan balance.',
            ], 422);
        }

        $payment = $loan->payments()->create($validatedData);

        return response()->json([
            'message' => 'Payment added successfully',
            'payment' => $payment,
            'remaining_balance' => $loan->fresh()->remaining_balance,
        ], 201);
    }
}
