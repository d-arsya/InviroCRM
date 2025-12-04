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
        $priceList = [
            'MDW' => [
                '1' => 7000,
                '2' => 6000,
            ],
            'RO' => [
                '1' => 10000,
                '2' => 9000,
            ],
            'Aqua' => [
                'Galon'  => 22500,
                '220ML'  => 35000,
                '330ML'  => 40000,
                '600ML'  => 50000,
                '10L'    => 19000,
            ],
            'Le Minerale' => [
                'Galon'  => 21000,
                '330ML'  => 41000,
                '600ML'  => 51000,
                '5L'     => 16000,
                '1500ML'     => 55000,
            ],
            'Sanqua' => [
                '120ML'  => 18000,
                '220ML'  => 21000,
                '330ML'  => 32000,
                '600ML'  => 35000,
                '1500ML' => 36000,
            ],
            'Pelangi' => [
                '120ML' => 23000,
                '220ML' => 23000,
                '220ML B' => 26000,
            ],
            'Akrap' => [
                '220ML' => 23000,
                '600ML' => 38000,
            ],
            'Vit' => [
                'Galon'   => 18000,
                '220ML' => 23000,
                '550ML' => 35000,
                '1500ML' => 38000,
            ],
            'Gas' => [
                '3KG'   => 23000,
                '5,5KG'   => 110000,
                '12KG'   => 215000,
            ],
        ];

        $productCount = fake()->numberBetween(1, 10);

        // Flatten price list ke 1 list produk lengkap
        $flatList = [];
        foreach ($priceList as $brand => $items) {
            foreach ($items as $unit => $price) {
                $flatList[] = [
                    'brand' => $brand,
                    'unit'  => $unit,
                    'price' => $price,
                ];
            }
        }

        $products = collect(range(1, $productCount))->map(function () use ($flatList) {
            $selected = collect($flatList)->random();

            $quantity = fake()->numberBetween(1, 5);

            return [
                'name' => $selected['brand'] . ' ' . $selected['unit'],
                'quantity' => $quantity,
                'price' => $selected['price'],
                'total' => $quantity * $selected['price'],
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
