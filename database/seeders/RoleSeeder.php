<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPermissions = Permission::all();
        foreach (PermissionSeeder::ROLE_PERMISSIONS as $roleName => $patterns) {

            $role = Role::factory()->create([
                'name' => $roleName,
                'display_name' => ucfirst(str_replace('_', ' ', $roleName)),
            ]);

            $matchedIds = $allPermissions->filter(function ($permission) use ($patterns) {
                foreach ($patterns as $pattern) {
                    if ($pattern === '*') return true;
                    if (str_contains($permission->name, $pattern)) return true;
                }
                return false;
            })->pluck('id');

            $role->permissions()->attach($matchedIds);
        }
    }
}
