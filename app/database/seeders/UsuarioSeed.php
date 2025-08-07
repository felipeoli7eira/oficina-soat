<?php

namespace Database\Seeders;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use App\Modules\Usuario\Model\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeed extends Seeder
{
    public function run(): void
    {
        $usuarioAtendente = Usuario::factory()->create([
            'nome'     => 'Atendente',
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::ATENDENTE->value);

        $usuarioComercial = Usuario::factory()->create([
            'nome'     => 'Comercial',
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::COMERCIAL->value);

        $usuarioMecanico = Usuario::factory()->create([
            'nome'     => 'Mecanico',
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::MECANICO->value);

        $usuarioGestorEstoque = Usuario::factory()->create([
            'nome'     => 'Gestor Estoque',
            'status'   => StatusUsuario::ATIVO->value,
        ])->assignRole(Papel::GESTOR_ESTOQUE->value);
    }
}
