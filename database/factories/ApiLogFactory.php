<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiLog>
 */
class ApiLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'endpoint' => fake()->randomElement(['/api/license/validate', '/api/license/activate', '/api/license/deactivate']),
            'ip_address' => fake()->ipv4(),
            'license_key' => fake()->optional()->regexify('[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}'),
            'domain' => fake()->optional()->domainName(),
            'user_agent' => fake()->userAgent(),
            'status_code' => fake()->randomElement([200, 201, 400, 401, 403, 404, 422, 500]),
        ];
    }
}