<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * For one-off edge-case tests only: real splits come from RecordExpense.
 *
 * @extends Factory<ExpenseSplit>
 */
class ExpenseSplitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'expense_id' => Expense::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->numberBetween(100, 5_000),
        ];
    }
}
