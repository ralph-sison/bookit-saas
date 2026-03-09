<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(2),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'timezone' => 'UTC',
            'currency' => 'USD',
            'settings' => [],
            'is_active' => true,
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'trial_ends_at' => now()->subDay(),
        ]);
    }
}
