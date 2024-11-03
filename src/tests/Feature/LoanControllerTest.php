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

    protected function setUp(): void
{
    parent::setUp();
    $this->app['env'] = 'testing';
    $this->withoutExceptionHandling();
}
}
