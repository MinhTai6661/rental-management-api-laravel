<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wardCodes = DB::table('wards')->pluck('code')->toArray();
        $roleIds = DB::table('roles')->pluck('id', 'name');

        if (empty($wardCodes)) {
            $this->command->error('Bảng wards trống! Hãy seed ProvinceSeeder trước.');
            return;
        }

        $adminUsers = [
            ['email' => 'superadmin@rental.com', 'role' => 'super_admin'],
            ['email' => 'admin@rental.com', 'role' => 'admin'],
            ['email' => 'tenant@rental.com', 'role' => 'tenant'],
        ];

        foreach ($adminUsers as $adminUser) {
            $user = User::factory()->create([
                'email' => $adminUser['email'],
                'ward_code' => $wardCodes[array_rand($wardCodes)],
            ]);

            DB::table('user_has_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $roleIds[$adminUser['role']],
            ]);
        }

        $this->command->info('Đang tạo 10.000 users...');

        for ($i = 0; $i < 100; $i++) {
            User::factory()->count(100)->create([
                'ward_code' => fn() => $wardCodes[array_rand($wardCodes)],
            ]);
        }

        $this->command->info('Đã hoàn thành seed User!');
    }
}
