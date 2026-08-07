<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Users & Roles
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',

            // Master Data
            'branches.view', 'branches.create', 'branches.update', 'branches.delete',
            'departments.view', 'departments.create', 'departments.update', 'departments.delete',
            'employees.view', 'employees.create', 'employees.update', 'employees.delete',
            'countries.view', 'countries.create', 'countries.update', 'countries.delete',
            'cities.view', 'cities.create', 'cities.update', 'cities.delete',
            'currencies.view', 'currencies.create', 'currencies.update', 'currencies.delete',
            'airlines.view', 'airlines.create', 'airlines.update', 'airlines.delete',
            'airports.view', 'airports.create', 'airports.update', 'airports.delete',
            'form-owners.view', 'form-owners.create', 'form-owners.update', 'form-owners.delete',
            'maktab-categories.view', 'maktab-categories.create', 'maktab-categories.update', 'maktab-categories.delete',
            'care-offs.view', 'care-offs.create', 'care-offs.update', 'care-offs.delete',
            'packages.view', 'packages.create', 'packages.update', 'packages.delete',
            'room-types.view', 'room-types.create', 'room-types.update', 'room-types.delete',
            'mehram-relations.view', 'mehram-relations.create', 'mehram-relations.update', 'mehram-relations.delete',
            'waris-relations.view', 'waris-relations.create', 'waris-relations.update', 'waris-relations.delete',
            'hotels.view', 'hotels.create', 'hotels.update', 'hotels.delete',
            'transporters.view', 'transporters.create', 'transporters.update', 'transporters.delete',
            'guides.view', 'guides.create', 'guides.update', 'guides.delete',
            'vendors.view', 'vendors.create', 'vendors.update', 'vendors.delete',
            'items.view', 'items.create', 'items.update', 'items.delete',
            'taxes.view', 'taxes.create', 'taxes.update', 'taxes.delete',

            // Customers
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',

            // Suppliers
            'suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.delete',
            'suppliers.portal.manage',

            // CRM
            'leads.view', 'leads.create', 'leads.update', 'leads.delete',
            'leads.assign', 'leads.import',

            // Invoices
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.delete',
            'invoices.post', 'invoices.refund',

            // Umrah
            'umrah.view', 'umrah.create', 'umrah.update', 'umrah.refund',
            'umrah.documents.manage',

            // Tours
            'tours.view', 'tours.create', 'tours.update', 'tours.refund',
            'tours.payment.receive',

            // Visa
            'visa.view', 'visa.status.update', 'visa.documents.manage',

            // Payments
            'receipts.create', 'receipts.update', 'receipts.delete',
            'supplier.payments.create', 'supplier.payments.update',
            'refunds.process',

            // Accounting
            'coa.view', 'coa.manage',
            'journals.view', 'journals.create',
            'vouchers.view', 'vouchers.create', 'vouchers.update', 'vouchers.delete',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete',
            'periods.close',

            // Reports
            'reports.sales.view',
            'reports.ledger.view',
            'reports.financial.view',
            'reports.export',

            // Settings
            'settings.manage',

            // Company
            'companies.view', 'companies.create', 'companies.edit', 'companies.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin — all permissions (also bypassed via Gate::before)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('name', 'not like', 'roles.%')->get());

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'customers.view', 'suppliers.view',
            'invoices.view', 'invoices.post',
            'receipts.create', 'receipts.update', 'receipts.delete',
            'supplier.payments.create', 'supplier.payments.update',
            'vouchers.view', 'vouchers.create', 'vouchers.update', 'vouchers.delete',
            'expenses.view', 'expenses.create', 'expenses.update', 'expenses.delete',
            'coa.view', 'journals.view', 'journals.create',
            'reports.sales.view', 'reports.ledger.view', 'reports.financial.view', 'reports.export',
            'periods.close',
        ]);

        // Sales Manager
        $salesManager = Role::firstOrCreate(['name' => 'Sales Manager', 'guard_name' => 'web']);
        $salesManager->syncPermissions([
            'customers.view', 'customers.create', 'customers.update',
            'leads.view', 'leads.create', 'leads.update', 'leads.delete', 'leads.assign', 'leads.import',
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.refund',
            'reports.sales.view', 'reports.export',
        ]);

        // Sales Agent
        $salesAgent = Role::firstOrCreate(['name' => 'Sales Agent', 'guard_name' => 'web']);
        $salesAgent->syncPermissions([
            'customers.view', 'customers.create',
            'leads.view', 'leads.create', 'leads.update',
            'invoices.view', 'invoices.create',
        ]);

        // Operations Manager
        $opsManager = Role::firstOrCreate(['name' => 'Operations Manager', 'guard_name' => 'web']);
        $opsManager->syncPermissions([
            'customers.view', 'suppliers.view',
            'invoices.view', 'tours.view', 'tours.create', 'tours.update',
            'umrah.view', 'umrah.create', 'umrah.update',
            'visa.view', 'visa.status.update', 'visa.documents.manage',
        ]);

        // Visa Officer
        $visaOfficer = Role::firstOrCreate(['name' => 'Visa Officer', 'guard_name' => 'web']);
        $visaOfficer->syncPermissions([
            'customers.view', 'suppliers.view',
            'invoices.view',
            'visa.view', 'visa.status.update', 'visa.documents.manage',
        ]);

        // Umrah Officer
        $umrahOfficer = Role::firstOrCreate(['name' => 'Umrah Officer', 'guard_name' => 'web']);
        $umrahOfficer->syncPermissions([
            'customers.view', 'suppliers.view',
            'umrah.view', 'umrah.create', 'umrah.update', 'umrah.refund',
            'umrah.documents.manage',
        ]);

        // Tour Officer
        $tourOfficer = Role::firstOrCreate(['name' => 'Tour Officer', 'guard_name' => 'web']);
        $tourOfficer->syncPermissions([
            'customers.view', 'suppliers.view',
            'tours.view', 'tours.create', 'tours.update', 'tours.refund',
            'tours.payment.receive',
        ]);

        // Auditor — read only
        $auditor = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);
        $auditor->syncPermissions([
            'invoices.view', 'vouchers.view', 'expenses.view',
            'coa.view', 'journals.view',
            'reports.sales.view', 'reports.ledger.view', 'reports.financial.view', 'reports.export',
        ]);

        // Supplier User — for supplier portal (separate guard later)
        Role::firstOrCreate(['name' => 'Supplier User', 'guard_name' => 'web']);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
