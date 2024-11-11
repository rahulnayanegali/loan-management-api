<?php

namespace Tests\Feature;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_payment_to_loan()
    {
        $loan = Loan::factory()->create(['amount' => 1000]);

        $response = $this->postJson("/api/loans/{$loan->id}/payments", [
            'amount' => 500
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'payment' => ['id', 'loan_id', 'amount', 'created_at', 'updated_at'],
                     'remaining_balance'
                 ]);

        $this->assertDatabaseHas('payments', [
            'loan_id' => $loan->id,
            'amount' => 500
        ]);
    }

    public function test_cannot_add_payment_exceeding_loan_amount()
    {
        $loan = Loan::factory()->create(['amount' => 1000]);

        $response = $this->postJson("/api/loans/{$loan->id}/payments", [
            'amount' => 1500
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Payment amount exceeds the remaining loan balance.'
                 ]);

        $this->assertDatabaseMissing('payments', [
            'loan_id' => $loan->id,
            'amount' => 1500
        ]);
    }

    public function test_cannot_add_payment_to_nonexistent_loan()
    {
        $nonexistentLoanId = 9999; // Assuming this ID doesn't exist

        $response = $this->postJson("/api/loans/{$nonexistentLoanId}/payments", [
            'amount' => 500
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Loan not found.'
                ]);

        $this->assertDatabaseMissing('payments', [
            'loan_id' => $nonexistentLoanId,
            'amount' => 500
        ]);
    }
}
