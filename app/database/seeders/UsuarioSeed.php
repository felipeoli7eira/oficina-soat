<?php

namespace Database\Seeders;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeed extends Seeder
{
    public function run(): void
    {
        $usuarioAtendente = Usuario::factory()->create([
            'nome'     => 'Atendente',
            'email'    => 'atendente@example.com',
            'senha'    => Hash::make('senha8caracteres'),
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::ATENDENTE->value)->guard(['api']);

        $usuarioComercial = Usuario::factory()->create([
            'nome'     => 'Comercial',
            'email'    => 'comercial@example.com',
            'senha'    => Hash::make('senha8caracteres'),
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::COMERCIAL->value)->guard(['api']);

        $usuarioMecanico = Usuario::factory()->create([
            'nome'     => 'Mecanico',
            'email'    => 'mecanico@example.com',
            'senha'    => Hash::make('senha8caracteres'),
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::MECANICO->value)->guard(['api']);

        $usuarioGestorEstoque = Usuario::factory()->create([
            'nome'     => 'Gestor Estoque',
            'email'    => 'gestor_estoque@example.com',
            'senha'    => Hash::make('senha8caracteres'),
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::GESTOR_ESTOQUE->value)->guard(['api']);
    }
}
