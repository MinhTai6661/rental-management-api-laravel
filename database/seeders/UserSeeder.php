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
        $defaultUsers = [
            [
                'email' => 'superadmin@rental.com',
                'password' => '12345678',
                'name' => 'Super Admin',
                'role' => 'super_admin'
            ],
            [
                'email' => 'admin@rental.com',
                'password' => '12345678',
                'name' => 'Default Admin',
                'role' => 'admin'
            ],
            [
                'email' => 'landlord@rental.com',
                'password' => '12345678',
                'name' => 'Default Landlord',
                'role' => 'landlord'
            ],
            [
                'email' => 'tenant@rental.com',
                'password' => '12345678',
                'name' => 'Default Tenant',
                'role' => 'tenant'
            ],
        ];

        foreach ($defaultUsers as $index => $defaultUser) {
            $user = User::factory()->create([
                'name' => $defaultUser['name'],
                'email' => $defaultUser['email'],
                'password' => bcrypt($defaultUser['password']),
            ]);
            $user->roles()->attach($roleIds[$defaultUser['role']]);
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
