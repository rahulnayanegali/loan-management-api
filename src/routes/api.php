<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;

Route::middleware('api')->group(function () {
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
    Route::put('/loans/{id}', [LoanController::class, 'update']);
    Route::delete('/loans/{id}', [LoanController::class, 'destroy']);
    Route::get('/loans', [LoanController::class, 'index']);
    Route::post('/loans/{loanId}/payments', [PaymentController::class, 'store']);
});


