<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',

            'profile.view',
            'profile.password',



            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            'lead-sources.view',
            'lead-sources.create',
            'lead-sources.edit',
            'lead-sources.delete',

            'lead-stages.view',
            'lead-stages.create',
            'lead-stages.edit',
            'lead-stages.delete',

            'lead-requirements.view',
            'lead-requirements.create',
            'lead-requirements.edit',
            'lead-requirements.delete',

            'lost-reasons.view',
            'lost-reasons.create',
            'lost-reasons.edit',
            'lost-reasons.delete',

            'followups.view',
            'followups.create',
            'followups.edit',
            'followups.delete',

            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',

            'general-settings.view',
            'general-settings.edit',
            'referral-settings.view',
            'referral-settings.edit',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $adminUser = User::orderBy('id')->first();
        if ($adminUser) {
            $adminUser->assignRole($superAdmin);
        }
    }
}
