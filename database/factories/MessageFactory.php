<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $text = fake()->sentence(6) . ' {nama}\n\n' . fake()->paragraph();

        return [
            'title' => fake()->sentence(4),
            'text' => $text,
            'default' => fake()->boolean(),
        ];
    }
}
