<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(10),
            'version' => fake()->semver(),
            'price' => fake()->optional(0.7)->randomFloat(2, 0, 999),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }
}