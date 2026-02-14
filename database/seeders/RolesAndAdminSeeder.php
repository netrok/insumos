<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['ADMIN', 'ALMACEN', 'COMPRAS', 'SOLICITANTE', 'AUDITOR'];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        // Limpia el rol “Admin” si existe (mal escrito)
        Role::where('name', 'Admin')->delete();

        $admin = User::firstOrCreate(
            ['email' => 'admin@insumos.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin12345!'),
            ]
        );

        // Asigna el rol correcto
        $admin->syncRoles(['ADMIN']);
    }
}
