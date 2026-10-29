<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Household;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Splits are not generated: they must sum to the amount, which is RecordExpense's job.
 *
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'paid_by' => User::factory(),
            'description' => fake()->randomElement([
                'Groceries', 'Electricity', 'Gas', 'Water', 'Broadband', 'Cleaning supplies', 'Takeaway',
            ]),
            'amount' => fake()->numberBetween(100, 50_000),
            'category' => null,
            'spent_on' => fake()->dateTimeBetween('-1 month'),
        ];
    }
}
