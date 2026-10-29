<?php

namespace Database\Factories;

use App\Enums\SettlementStatus;
use App\Models\Household;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Settlement>
 */
class SettlementFactory extends Factory
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
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'amount' => fake()->numberBetween(100, 50_000),
            'status' => SettlementStatus::Pending,
            'confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the settlement is awaiting the recipient's response.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SettlementStatus::Pending,
            'confirmed_at' => null,
        ]);
    }

    /**
     * Indicate that the recipient confirmed the settlement.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SettlementStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the recipient rejected the settlement.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SettlementStatus::Rejected,
            'confirmed_at' => null,
        ]);
    }
}
