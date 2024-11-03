<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'interest_rate' => $this->faker->randomFloat(2, 1, 20),
            'duration_months' => $this->faker->numberBetween(6, 60),
        ];
    }
}
