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
            'booking.view', 'booking.create', 'booking.edit', 'booking.delete',
            'payment.view', 'payment.invoice', 'payment.manual',
            'paket.view', 'paket.create', 'paket.edit', 'paket.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        // 1. Super Admin -> gets all permissions
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // 2. Seller -> gets specific permissions
        $sellerRole = Role::create(['name' => 'Seller']);
        $sellerRole->givePermissionTo([
            'dashboard.view',
            'booking.view', 'booking.create', 'booking.edit',
            'payment.view', 'payment.invoice',
        ]);

        // 3. User -> can only view dashboard (further logic in controllers)
        $userRole = Role::create(['name' => 'User']);
        $userRole->givePermissionTo(['dashboard.view']);


        // Create Users and Assign Roles
        // Ensure default users are created if they don't exist
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        )->assignRole($superAdminRole);

        User::firstOrCreate(
            ['email' => 'seller@gmail.com'],
            [
                'name' => 'Seller',
                'password' => Hash::make('password'),
            ]
        )->assignRole($sellerRole);

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
            ]
        )->assignRole($userRole);
    }
}
