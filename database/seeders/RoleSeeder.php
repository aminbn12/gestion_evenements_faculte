<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Events
            ['name' => 'View Events', 'slug' => 'view-events', 'module' => 'events'],
            ['name' => 'Create Events', 'slug' => 'create-events', 'module' => 'events'],
            ['name' => 'Edit Events', 'slug' => 'edit-events', 'module' => 'events'],
            ['name' => 'Delete Events', 'slug' => 'delete-events', 'module' => 'events'],
            
            // Alerts
            ['name' => 'Send Alerts', 'slug' => 'send-alerts', 'module' => 'alerts'],
            ['name' => 'View Alerts', 'slug' => 'view-alerts', 'module' => 'alerts'],
            
            // Users
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'module' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'module' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'module' => 'users'],
            
            // Roles
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'module' => 'roles'],
            
            // Profiles
            ['name' => 'View All Profiles', 'slug' => 'view-all-profiles', 'module' => 'profiles'],
            ['name' => 'Edit Own Profile', 'slug' => 'edit-own-profile', 'module' => 'profiles'],
            
            // Leaves
            ['name' => 'Approve Leaves', 'slug' => 'approve-leaves', 'module' => 'leaves'],
            ['name' => 'View All Leaves', 'slug' => 'view-all-leaves', 'module' => 'leaves'],
            
            // Evaluations
            ['name' => 'Create Evaluations', 'slug' => 'create-evaluations', 'module' => 'evaluations'],
            ['name' => 'View Evaluations', 'slug' => 'view-evaluations', 'module' => 'evaluations'],
            
            // Team
            ['name' => 'Manage Groups', 'slug' => 'manage-groups', 'module' => 'team'],
            ['name' => 'View Team', 'slug' => 'view-team', 'module' => 'team'],
            
            // Reports
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'module' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles with permissions
        $roles = [
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Full access to all features',
                'permissions' => Permission::pluck('id')->toArray(),
            ],
            [
                'name' => 'Chef Département',
                'slug' => 'chef-dept',
                'description' => 'Department management access',
                'permissions' => Permission::whereIn('slug', [
                    'view-events', 'create-events', 'edit-events',
                    'send-alerts', 'view-alerts',
                    'view-users',
                    'view-all-profiles', 'edit-own-profile',
                    'approve-leaves', 'view-all-leaves',
                    'create-evaluations', 'view-evaluations',
                    'view-team',
                    'export-reports',
                ])->pluck('id')->toArray(),
            ],
            [
                'name' => 'Enseignant',
                'slug' => 'enseignant',
                'description' => 'Teacher access',
                'permissions' => Permission::whereIn('slug', [
                    'view-events',
                    'edit-own-profile',
                    'view-team',
                ])->pluck('id')->toArray(),
            ],
            [
                'name' => 'Technicien',
                'slug' => 'technicien',
                'description' => 'Technical staff access',
                'permissions' => Permission::whereIn('slug', [
                    'view-events',
                    'edit-own-profile',
                    'view-team',
                ])->pluck('id')->toArray(),
            ],
            [
                'name' => 'Secrétaire',
                'slug' => 'secretaire',
                'description' => 'Secretary access',
                'permissions' => Permission::whereIn('slug', [
                    'view-events', 'create-events',
                    'edit-own-profile',
                    'view-team',
                ])->pluck('id')->toArray(),
            ],
            [
                'name' => 'Résidanat',
                'slug' => 'residanat',
                'description' => 'Resident access',
                'permissions' => Permission::whereIn('slug', [
                    'view-events',
                    'edit-own-profile',
                    'view-team',
                ])->pluck('id')->toArray(),
            ],
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::create($roleData);
            $role->permissions()->sync($permissions);
        }
    }
}
