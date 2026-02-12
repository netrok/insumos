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
        $roles = ['ADMIN', 'ALMACEN', 'COMPRAS', 'SOLICITANTE', 'AUDITOR'];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@insumos.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin12345!'),
            ]
        );

        if (! $admin->hasRole('ADMIN')) {
            $admin->assignRole('ADMIN');
        }
    }
}
