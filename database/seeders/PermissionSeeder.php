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

            'incentives.view',
            'incentives.create',
            'incentives.edit',
            'incentives.delete',

            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            'lead-sources.view',
            'lead-sources.create',
            'lead-sources.edit',
            'lead-sources.delete',

            'coordinations.view',
            'coordinations.create',
            'coordinations.edit',
            'coordinations.delete',

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
            'followups.reassign',
            'staff.leave',

            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',

            'general-settings.view',
            'general-settings.edit',
            'lead-settings.view',
            'lead-settings.edit',
            'customer-settings.view',
            'customer-settings.edit',
            'followup-settings.view',
            'followup-settings.edit',
            'credit-request-settings.view',
            'credit-request-settings.edit',
            'demo-process-settings.view',
            'demo-process-settings.edit',

            'lead-documents.view',
            'lead-documents.create',
            'lead-documents.edit',
            'lead-documents.delete',

            'templates.view',
            'templates.create',
            'templates.edit',
            'templates.delete',

            'call-recordings.view',
            'call-recordings.create',
            'call-recordings.edit',
            'call-recordings.delete',

            'call-logs.view',
            'call-logs.create',
            'call-logs.edit',
            'call-logs.delete',
            'call-log-reports.view',

            'attendance.view',
            'attendance.create',
            'attendance.edit',
            'attendance.delete',
            'attendance-reports.view',

            'leaves.view',
            'leaves.create',
            'leaves.approve',
            'leaves.delete',
            'salary.view',

            'credit-requests.view',
            'credit-requests.create',
            'credit-requests.approve_admin',
            'credit-requests.approve_support',
            'credit-requests.delete',

            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',

            'permissions.view',
            'permissions.create',
            'permissions.approve',
            'permissions.delete',

            'demo-processes.view',
            'demo-processes.create',
            'demo-processes.edit',
            'demo-processes.delete',
            'demo-processes.assign',
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
