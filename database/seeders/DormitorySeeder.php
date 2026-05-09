<?php

namespace Database\Seeders;

use App\Models\Dormitory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DormitorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landlordsIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'landlord');
        })->pluck('id')->toArray();

        foreach ($landlordsIds as $landlordId) {
            Dormitory::factory()->count(2)->create([
                'landlord_id' => $landlordId,
            ]);
        }


        $usersIds = User::pluck('id')->toArray();
        Dormitory::factory()->count(10)->create([
            'landlord_id' => fake()->randomElement($usersIds),
        ]);
    }
}
