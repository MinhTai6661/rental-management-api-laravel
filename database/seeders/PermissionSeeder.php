<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public const ROLE_PERMISSIONS = [
        'super_admin' => ['*'],
        'admin'       => ['users.', 'dormitories.', 'rooms.', 'invoices.', 'reports.'],
        'landlord'     => ['dormitories.', 'rooms.', 'invoices.', 'reports.'],
        'tenant'        => ['rooms.view', 'invoices.view'],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $resources = ['users', 'dormitories', 'rooms', 'invoices', 'reports'];
        $actions = ['view', 'create', 'update', 'delete'];

        $permissions = [];
        foreach ($resources as $res) {
            foreach ($actions as $act) {
                $permissions[] = [
                    'name' => "$res.$act",
                    'description' => 'Cho phép ' . $act . ' trong mục ' . $res,
                ];
            }
        }

        Permission::factory()->createMany($permissions);
    }
}
