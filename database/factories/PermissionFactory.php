<?php

namespace Database\Factories;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Permission>
 */
#[UseModel(Permission::class)]
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resources = ['users', 'dormitories', 'rooms', 'invoices', 'reports'];
        $actions = ['view', 'create', 'update', 'delete'];
    
        return [
            'name' => fake()->randomElement($resources) . '.' . fake()->randomElement($actions),
            'description' => fake()->sentence(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => []);
    }
}
