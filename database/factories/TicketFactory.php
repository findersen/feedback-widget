<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['new', 'in_progress', 'done']);

        return [
            'customer_id' => Customer::factory(),
            'subject' => $this->faker->sentence(5),
            'message' => $this->faker->paragraphs(asText: true),
            'status' => $status,
            'manager_answered_at' => $status === 'processed'
                ? Carbon::now()->subDays($this->faker->numberBetween(0, 14))
                : null,
        ];
    }
}
