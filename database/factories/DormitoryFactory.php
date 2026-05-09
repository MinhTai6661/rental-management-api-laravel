<?php

namespace Database\Factories;

use App\Models\Dormitory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dormitory>
 */
class DormitoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'description' => $this->faker->paragraph(),
            'landlord_id' => \App\Models\User::factory(),
            'ward_code' => \App\Models\Ward::inRandomOrder()->first()?->code,
        ];
    }
}
