<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['ADMIN', 'ALMACEN', 'COMPRAS', 'SOLICITANTE', 'AUDITOR'];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@insumos.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin12345!'),
            ]
        );

        $admin->syncRoles(['ADMIN']);
    }

}
