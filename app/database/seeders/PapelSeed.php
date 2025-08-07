<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PapelSeed extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name'       => 'atendente',
            'guard_name' => 'api',
        ]);

        Role::create([
            'name'       => 'comercial',
            'guard_name' => 'api',
        ]);

        Role::create([
            'name'       => 'mecanico',
            'guard_name' => 'api',
        ]);

        Role::create([
            'name'       => 'gestor_estoque',
            'guard_name' => 'api',
        ]);
    }
}
