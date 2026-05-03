<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InitialSeeder extends Seeder
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

        $wardCodes = DB::table('wards')->pluck('code')->toArray();

        if (empty($wardCodes)) {
            $this->command->error('Bảng wards đang trống! Hãy seed bảng wards trước.');
            return;
        }

        // Seed users
        $users = [
            [
                'id' => (string) Str::uuid(),
                'email' => 'superadmin@rental.com',
                'password' => bcrypt('admin123456'),
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'status' => 'active',
                'phone' => '+84901234567',
                'ward_code' => $wardCodes[array_rand($wardCodes)] ?? null,
                'detail_address' => '123 Đường Nguyễn Huệ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'email' => 'admin@rental.com',
                'password' => bcrypt('admin123456'),
                'name' => 'Admin User',
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+84902345678',
                'ward_code' => $wardCodes[array_rand($wardCodes)] ?? null,
                'detail_address' => '456 Đường Lê Lợi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'email' => 'tenant@rental.com',
                'password' => bcrypt('admin123456'),
                'name' => 'Tenant User',
                'role' => 'user',
                'status' => 'active',
                'phone' => '+84903456789',
                'ward_code' => $wardCodes[array_rand($wardCodes)] ?? null,
                'detail_address' => '789 Đường Thạch Thị Thanh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];


        DB::table('users')->insert($users);

        // Thông báo tạo thành công các user đặc biệt
        foreach ($users as $user) {
            $role = $user['role'] === 'super_admin' ? 'Super Admin' : ($user['role'] === 'admin' ? 'Admin' : 'Tenant');
            $this->command->info("✅ {$role} created: {$user['email']} / admin123456");
        }

        $this->command->info('Đang tạo 10,000 users...');

        // Chia nhỏ thành 10 đợt, mỗi đợt 1000 user để không treo RAM
        foreach (range(1, 10) as $i) {
            \App\Models\User::factory()->count(100)->create([
                'ward_code' => function () use ($wardCodes) {
                    return $wardCodes[array_rand($wardCodes)];
                }
            ]);
            $this->command->comment("Đã xong " . ($i * 100) . " users");
        }

        $this->command->info('Hoàn thành!');

        // Seed plans
        $plans = [
            [
                'id' => (string) Str::uuid(),
                'type' => 'free',
                'name' => 'Free Plan',
                'description' => 'Basic plan for getting started',
                'price' => 0,
                'max_rooms' => 5,
                'flags' => 1,
                'channels' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'pro',
                'name' => 'Professional Plan',
                'description' => 'Perfect for growing property managers',
                'price' => 99.99,
                'max_rooms' => 50,
                'flags' => 3,
                'channels' => json_encode(['email', 'sms']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'type' => 'ultra',
                'name' => 'Ultra Plan',
                'description' => 'Enterprise solution with unlimited features',
                'price' => 499.99,
                'max_rooms' => 999,
                'flags' => 7,
                'channels' => json_encode(['email', 'sms', 'push']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('plans')->insert($plans);

        // Seed dormitory
        $admin_id = $users[1]['id'];
        $dormitory_id = (string) Str::uuid();
        DB::table('dormitories')->insert([
            'id' => $dormitory_id,
            'name' => 'Modern Apartment Complex A',
            'address' => '123 Main Street, District 1, Ho Chi Minh City',
            'description' => 'Modern apartment with excellent amenities',
            'image' => 'https://example.com/dorm1.jpg',
            'admin_id' => $admin_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed rooms
        for ($i = 1; $i <= 5; $i++) {
            DB::table('rooms')->insert([
                'id' => (string) Str::uuid(),
                'room_number' => "A{$i}01",
                'area' => 30 + $i * 5,
                'rental_price' => 5000000 + $i * 500000,
                'description' => 'Spacious room with balcony',
                'status' => $i <= 2 ? 'rented' : 'empty',
                'dormitory_id' => $dormitory_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }



        // Thông báo tạo thành công các user đặc biệt
        foreach ($users as $user) {
            $role = $user['role'] === 'super_admin' ? 'Super Admin' : ($user['role'] === 'admin' ? 'Admin' : 'Tenant');
            $this->command->info("✅ {$role} created: {$user['email']} / admin123456");
        }
    }
}
