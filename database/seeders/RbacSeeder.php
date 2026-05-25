<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // User & Account
            'users.view_any', 'users.view', 'users.create', 'users.update', 'users.delete', 'users.assign_role',
            'profile.view_own', 'profile.update_own',
            // Candidates / Employees (HR scoped, no login)
            'candidates.view', 'candidates.create', 'candidates.update', 'candidates.delete',
            // Master data (departments & positions, org-scoped)
            'master_data.view', 'master_data.manage',
            // Assessment batches (HR scoped)
            'batches.view', 'batches.create', 'batches.update', 'batches.delete',
            // Assessment & Results (staff only — guests take via share link)
            'assessments.manage_global', 'assessments.manage_org',
            'results.view_org', 'results.view_any',
            'results.share_create', 'results.share_revoke', 'results.share_view', 'results.download_pdf',
            'results.delete',
            // Payment & Voucher
            'invoices.view_any', 'invoices.view_own',
            'vouchers.manage', 'vouchers.validate',
            'payments.config',
            // Admin & System
            'admin.dashboard', 'admin.reports.export', 'admin.ai_monitoring',
            'system.config', 'system.audit_log',
            // Organizations (multi-tenant)
            'organizations.manage', 'organizations.view_own', 'organizations.update_own',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superadmin = Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->givePermissionTo(
            Permission::query()
                ->where('name', 'not like', 'master_data.%')
                ->pluck('name')
        );

        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'profile.view_own', 'profile.update_own',
            'organizations.view_own', 'organizations.update_own',
            'results.view_org',
            'results.share_create', 'results.share_revoke', 'results.share_view', 'results.download_pdf',
            'admin.dashboard', 'admin.reports.export', 'admin.ai_monitoring',
            'invoices.view_own', 'vouchers.validate',
            'users.view',
            'candidates.view', 'candidates.create', 'candidates.update', 'candidates.delete',
            'batches.view', 'batches.create', 'batches.update', 'batches.delete',
            'assessments.manage_org',
            'master_data.view', 'master_data.manage',
        ]);

        // Kandidat: data profil saja, tidak bisa login & tidak punya permission
        Role::create(['name' => 'candidate', 'guard_name' => 'web']);
    }
}
