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
        $productCount = fake()->numberBetween(1, 10);

        $products = collect(range(1, $productCount))->map(function () {
            $quantity = fake()->numberBetween(1, 5);
            $price = fake()->numberBetween(10, 20) * 1000;

            return [
                'name' => fake()->word(),
                'quantity' => $quantity,
                'price' => $price,
                'total' => $quantity * $price,
            ];
        })->toArray();
        $phones = explode(',', env('TEAM_NUMBER'));

        return [
            'name' => fake()->name(),
            'phone' => fake()->randomElement($phones),
            'date' => fake()->date(),
            'total_price' => collect($products)->sum('total'),
            'total_count' => collect($products)->sum('quantity'),
            'products' => $products,
        ];
    }
}
