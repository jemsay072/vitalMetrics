<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Permission

        $admin = Role::where('name', 'Admin')->firstOrFail();

        $adminPermissions = Permission::whereIn('name', [
            'dashboard',
            'trackers',
            'users',
            'profile',
            'roles',
            'settings',
            'export',
            'reports',
        ])->pluck('id')->all();

        $admin->permissions()->syncWithoutDetaching(
            $adminPermissions
        );

        // User Permissions
        $user = Role::where('name', 'User')->firstOrFail();
        $userPermissions = Permission::whereIn('name', [
            'dashboard',
            'trackers',
            'profile',
            'export',
        ])->pluck('id')->all();

        $user->permissions()->syncWithoutDetaching(
            $userPermissions
        );
    }
}
