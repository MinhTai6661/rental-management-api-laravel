<?php

use Database\Seeders\InitialSeeder;
use App\Models\User;
use Database\Seeders\DormitorySeeder;
use Database\Seeders\OrderResourcesSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            // InitialSeeder::class,
            OrderResourcesSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            DormitorySeeder::class,
            RoomSeeder::class,
        ]);


    }
}
