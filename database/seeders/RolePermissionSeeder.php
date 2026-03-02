<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Hapus data lama untuk menghindari duplikat saat seeder dijalankan ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // Create Permissions for all modules
        $permissions = [
            'dashboard.view',
            'user.view', 'user.create', 'user.edit', 'user.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'booking.view', 'booking.edit', 'booking.delete',
            'payment.view', 'payment.invoice',
            'paket.view', 'paket.create', 'paket.edit', 'paket.delete',
            'vendor.view', 'vendor.create', 'vendor.edit', 'vendor.delete',
            'area.view', 'area.create', 'area.edit', 'area.delete',
            'setting.view', 'setting.edit',
            'review.view', 'review.approve', 'review.reply', 'review.delete',
            'report.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        // 1. Super Admin -> gets all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // 2. Vendor -> gets specific permissions for their own data
        $vendorRole = Role::firstOrCreate(['name' => 'Vendor']);
        $vendorRole->syncPermissions([
            'dashboard.view',
            'booking.view',
            'payment.view', 'payment.invoice',
            'paket.view', 'paket.create', 'paket.edit', 'paket.delete',
            'review.view', 'review.reply',
            'report.view',
        ]);

        // Create Users and Assign Roles
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($superAdminRole);

        $vendorUser = User::updateOrCreate(
            ['email' => 'vendor@gmail.com'],
            [
                'name' => 'Mitra Vendor',
                'password' => Hash::make('password'),
            ]
        );
        $vendorUser->assignRole($vendorRole);
    }
}
