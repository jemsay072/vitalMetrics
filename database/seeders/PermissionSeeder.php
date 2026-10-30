<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'dashboard',
                'label' => 'Dashboard',
                'description' => 'Access the dashboard',
            ],
            [
                'name' => 'trackers',
                'label' => 'Trackers',
                'description' => 'Access health trackers',
            ],
            [
                'name' => 'users',
                'label' => 'Users',
                'description' => 'Manage user accounts',
            ],
            [
                'name' => 'profile',
                'label' => 'Profile',
                'description' => 'Access user profile',
            ],
            [
                'name' => 'roles',
                'label' => 'Roles',
                'description' => 'Manage roles and permissions',
            ],
            [
                'name' => 'patients',
                'label' => 'Patients',
                'description' => 'Access assigned patient records',
            ],
            [
                'name' => 'settings',
                'label' => 'Settings',
                'description' => 'Manage application settings',
            ],
            [
                'name' => 'export',
                'label' => 'Export',
                'description' => 'Export application data',
            ],
            [
                'name' => 'reports',
                'label' => 'Reports',
                'description' => 'Access reports',
            ],
            [
                'name' => 'view_only',
                'label' => 'View Only',
                'description' => 'View records without modifying them',
            ],
        ];

        foreach( $permissions as $permission ){
            Permission::updateOrCreate(
                [ 'name' => $permission['name'] ],
                [
                    'label' => $permission['label'],
                    'description' => $permission['description'],
                ]
            );
        }
    }
}
