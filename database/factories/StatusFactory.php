<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Status>
 */
class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'color' => $this->faker->hexColor(),
            'is_check_due' => $this->faker->boolean(),
        ];
    }

        /**
     * State: is_check_due = true
     */
    public function checkDue(): static
    {
        return $this->state(fn () => [
            'is_check_due' => 1,
        ]);
    }

    /**
     * State: is_check_due = false
     */

    public function noCheckDue(): static
    {
        return $this->state(fn () => [
            'is_check_due' => 0,
        ]);
    }
}
