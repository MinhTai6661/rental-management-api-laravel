<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OrderResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed provinces from API
        $provincesData = Http::get('https://provinces.open-api.vn/api/v2/p/')->json();
        foreach ($provincesData as $province) {
            DB::table('provinces')->insert([
                'code' => $province['code'],
                'name' => $province['name'],
                'division_type' => $province['division_type'],
                'codename' => $province['codename'],
                'phone_code' => $province['phone_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed wards from API
        $wardsData = Http::get('https://provinces.open-api.vn/api/v2/w/')->json();
        foreach ($wardsData as $ward) {
            DB::table('wards')->insert([
                'code' => $ward['code'],
                'name' => $ward['name'],
                'division_type' => $ward['division_type'],
                'codename' => $ward['codename'],
                'province_code' => $ward['province_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('seed provinces and wards successfully!!!!');


    }
}
