<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Models\Loan;


class LoanControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test creating a loan with valid data.
     */
    public function test_create_loan_with_valid_data(): void
    {
        $loanData = [
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'interest_rate' => $this->faker->randomFloat(2, 1, 20),
            'duration_months' => $this->faker->numberBetween(12, 360),
        ];

        $response = $this->postJson('/api/loans', $loanData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'loan' => [
                         'id',
                         'amount',
                         'interest_rate',
                         'duration_months',
                         'created_at',
                         'updated_at',
                     ]
                 ]);

        $this->assertDatabaseHas('loans', $loanData);
    }

    /**
     * Test attempting to create a loan with missing fields.
     */
    public function test_create_loan_with_missing_fields(): void
{
    $response = $this->postJson('/api/loans', []);

    $response->assertStatus(422)
             ->assertJsonStructure([
                 'message',
                 'errors' => [
                     'amount',
                     'interest_rate',
                     'duration_months'
                 ]
             ]);
}

    /**
     * Test attempting to create a loan with invalid data formats.
     */
    public function test_create_loan_with_invalid_data_formats(): void
{
    $invalidData = [
        'amount' => 'not a number',
        'interest_rate' => 'invalid',
        'duration_months' => 'not an integer',
    ];

    $response = $this->postJson('/api/loans', $invalidData);

    $response->assertStatus(422)
             ->assertJsonStructure([
                 'message',
                 'errors' => [
                     'amount',
                     'interest_rate',
                     'duration_months'
                 ]
             ])
             ->assertJsonValidationErrors([
                 'amount' => 'The loan amount must be a number.',
                 'interest_rate' => 'The interest rate must be a number.',
                 'duration_months' => 'The loan duration must be a whole number.'
             ]);
}

    /**
     * Test attempting to create a loan with out-of-range values.
     */
    public function test_create_loan_with_out_of_range_values(): void
{
    $invalidData = [
        'amount' => -1000,
        'interest_rate' => 101,
        'duration_months' => 0,
    ];

    $response = $this->postJson('/api/loans', $invalidData);

    $response->assertStatus(422)
             ->assertJsonStructure([
                 'message',
                 'errors' => [
                     'amount',
                     'interest_rate',
                     'duration_months'
                 ]
             ])
             ->assertJsonValidationErrors([
                 'amount' => 'The loan amount must be at least 0.',
                 'interest_rate' => 'The interest rate cannot exceed 100%.',
                 'duration_months' => 'The loan duration must be at least 1 month.'
             ]);
}

    /**
     * Test creating a loan with boundary values.
     */
    public function test_create_loan_with_boundary_values(): void
    {
        $boundaryData = [
            'amount' => 0,
            'interest_rate' => 100,
            'duration_months' => 1,
        ];

        $response = $this->postJson('/api/loans', $boundaryData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'loan' => [
                         'id',
                         'amount',
                         'interest_rate',
                         'duration_months',
                         'created_at',
                         'updated_at',
                     ]
                 ]);

        $this->assertDatabaseHas('loans', $boundaryData);
    }

    public function test_update_loan_with_valid_data()
{
    $loan = Loan::factory()->create();

    $updatedData = [
        'amount' => 6000,
        'interest_rate' => 6.5,
        'duration_months' => 24,
    ];

    $response = $this->putJson("/api/loans/{$loan->id}", $updatedData);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'message',
                 'loan' => [
                     'id',
                     'amount',
                     'interest_rate',
                     'duration_months',
                     'created_at',
                     'updated_at',
                 ]
             ]);

    $this->assertDatabaseHas('loans', $updatedData);
}

public function test_update_nonexistent_loan()
{
    $response = $this->putJson("/api/loans/999", ['amount' => 6000]);

    $response->assertStatus(404)
             ->assertJson(['message' => 'Loan not found']);
}

public function test_update_loan_with_invalid_data()
{
    $loan = Loan::factory()->create();

    $invalidData = [
        'amount' => -1000,
        'interest_rate' => 101,
        'duration_months' => 0,
    ];

    try {
        $response = $this->putJson("/api/loans/{$loan->id}", $invalidData);
    } catch (ValidationException $e) {
        $this->assertEquals(422, $e->status);
        $this->assertArrayHasKey('amount', $e->errors());
        $this->assertArrayHasKey('interest_rate', $e->errors());
        $this->assertArrayHasKey('duration_months', $e->errors());
        $this->assertContains('The amount field must be at least 0.', $e->errors()['amount']);
        $this->assertContains('The interest rate must not exceed 100.', $e->errors()['interest_rate']);
        $this->assertContains('The duration months must be at least 1.', $e->errors()['duration_months']);
        return;
    }

    $this->fail('ValidationException was not thrown');
}

public function test_update_loan_with_partial_data()
{
    // Create a loan with initial values
    $loan = Loan::factory()->create([
        'amount' => 5000,
        'interest_rate' => 5.5,
        'duration_months' => 12
    ]);

    // Prepare partial update data
    $partialUpdateData = [
        'amount' => 6000,
        'interest_rate' => 6.0
    ];

    // Send PUT request to update the loan
    $response = $this->putJson("/api/loans/{$loan->id}", $partialUpdateData);

    // Assert the response is successful
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'message',
                 'loan' => [
                     'id',
                     'amount',
                     'interest_rate',
                     'duration_months',
                     'created_at',
                     'updated_at',
                 ]
             ]);

    // Assert that the specified fields were updated
    $this->assertEquals(6000, $response->json('loan.amount'));
    $this->assertEquals(6.0, $response->json('loan.interest_rate'));

    // Assert that the unspecified field (duration_months) remains unchanged
    $this->assertEquals(12, $response->json('loan.duration_months'));

    // Verify the database was updated correctly
    $this->assertDatabaseHas('loans', [
        'id' => $loan->id,
        'amount' => 6000,
        'interest_rate' => 6.0,
        'duration_months' => 12
    ]);
}

public function test_delete_existing_loan()
{
    $loan = Loan::factory()->create();

    $response = $this->deleteJson("/api/loans/{$loan->id}");

    $response->assertStatus(200)
             ->assertJson(['message' => 'Loan deleted successfully']);

    $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
}

public function test_delete_nonexistent_loan()
{
    $response = $this->deleteJson("/api/loans/999");

    $response->assertStatus(404)
             ->assertJson(['message' => 'Loan not found']);
}

    protected function setUp(): void
{
    parent::setUp();
    $this->app['env'] = 'testing';
    $this->withoutExceptionHandling();
}

public function test_retrieve_loans_first_page_with_custom_pagination()
{
    Loan::factory()->count(20)->create();

    $response = $this->getJson("/api/loans?page=1&per_page=10");

    $response->assertStatus(200);
    $this->assertEquals(10, count($response->json('data')));
    $this->assertEquals(1, $response->json('meta.current_page'));
    $this->assertEquals(10, $response->json('meta.per_page'));
    $this->assertEquals(20, $response->json('meta.total'));
}
}
