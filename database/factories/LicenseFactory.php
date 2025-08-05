<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'license_key' => Str::upper(fake()->regexify('[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}')),
            'status' => fake()->randomElement(['active', 'expired', 'disabled', 'suspended']),
            'expires_at' => fake()->optional(0.8)->dateTimeBetween('now', '+2 years'),
            'max_activations' => fake()->numberBetween(1, 5),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}