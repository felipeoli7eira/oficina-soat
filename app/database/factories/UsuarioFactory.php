<?php

namespace Database\Factories;

use App\Enums\Papel;
use App\Modules\Usuario\Enums\StatusUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Usuario\Model\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = \App\Modules\Usuario\Model\Usuario::class;

    public array $possiveisPapeis = [
        Papel::MECANICO->value,
        Papel::COMERCIAL->value,
        Papel::ATENDENTE->value,
        Papel::GESTOR_ESTOQUE->value,
    ];

    public function definition(): array
    {
        $papel = fake()->randomElement($this->possiveisPapeis);

        return [
            'nome'    => fake()->name(),
            'status'  => StatusUsuario::ATIVO->value,
            'role_id' => Role::findByName($papel)->id,
        ];
    }
}
