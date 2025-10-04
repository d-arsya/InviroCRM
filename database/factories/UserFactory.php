<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->email(),
            'password' => Hash::make(fake()->text()),
            'last_login' => null,
        ];
    }
}
