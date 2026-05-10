<?php

namespace Database\Seeders;

use App\Models\Dormitory;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landLordDormitoriesIds = Dormitory::whereHas('landlord', function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', 'landlord');
            });
        })->pluck('id')->toArray();

        foreach ($landLordDormitoriesIds as $dormitoryId) {
            Room::factory()->count(20)->create([
                'dormitory_id' => $dormitoryId,
            ]);
        }

        Room::factory()->count(100)->create();
    }
}
