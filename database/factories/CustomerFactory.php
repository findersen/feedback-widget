<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Faker phoneNumber не гарантирует E.164 — делаем “псевдо E.164” стабильно
        $phone = '+' . $this->faker->numberBetween(1, 9) . $this->faker->numerify(str_repeat('#', $this->faker->numberBetween(9, 14)));

        return [
            'name'  => $this->faker->name(),
            'phone' => $phone,
            'email' => $this->faker->optional()->safeEmail(),
        ];
    }
}
