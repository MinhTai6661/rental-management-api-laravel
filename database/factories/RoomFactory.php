<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
#[UseModel(Room::class)]
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'size' => $this->faker->randomFloat(2, 10, 100),
            'rental_price' => $this->faker->randomFloat(2, 100, 1000),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['available', 'rented', 'repairing']),
            'dormitory_id' => \App\Models\Dormitory::factory(),
        ];
    }
}
