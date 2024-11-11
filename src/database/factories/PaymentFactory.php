<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition()
    {
        return [
            'loan_id' => Loan::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
