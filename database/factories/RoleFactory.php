<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Role>
 */
#[UseModel(Role::class)]
class RoleFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
            'display_name' => $this->faker->words(2, true),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'super_admin',
            'display_name' => 'quản trị viên cao cấp',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'admin',
            'display_name' => 'quản trị viên',
        ]);
    }

    public function landlord(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'landlord',
            'display_name' => 'chủ trọ',
        ]);
    }

    public function tenant(): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'tenant',
            'display_name' => 'người thuê trọ',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => []);
    }
}
