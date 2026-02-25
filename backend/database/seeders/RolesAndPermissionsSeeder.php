<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions by domain
        $permissions = [
            // Tenant management
            'tenant.view',
            'tenant.update',
            'tenant.manage-billing',

            // Services
            'services.view',
            'services.create',
            'services.update',
            'services.delete',

            // Staff
            'staff.view',
            'staff.create',
            'staff.update',
            'staff.delete',
            'staff.manage-schedule',

            // Bookings
            'bookings.view',
            'bookings.view-all',
            'bookings.create',
            'bookings.update',
            'bookings.cancel',

            // Customers
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',

            // Payments
            'payments.view',
            'payments.refund',

            // Settings
            'settings.view',
            'settings.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Owner - full access
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'sanctum']);
        $ownerRole->givePermissionTo(Permission::all());

        // Admin - everything except billing
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $adminRole->givePermissionTo(
            Permission::where('name', 'not like', '%.manage-billing')->get()
        );

        // Staff - limited to their own bookings and schedule
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'sanctum']);
        $staffRole->givePermissionTo([
            'services.view',
            'staff.view',
            'bookings.view',
            'bookings.create',
            'bookings.update',
            'customers.view',
            'customers.create'
        ]);

        // Customer - view and manage own bookings
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'sanctum']);
        $customerRole->givePermissionTo([
            'services.view',
            'staff.view',
            'bookings.view',
            'bookings.create',
            'bookings.cancel'
        ]);
    }
}
