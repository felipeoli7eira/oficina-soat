<?php

namespace Database\Factories;

use App\Modules\Usuario\Enums\StatusUsuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Usuario\Model\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = \App\Modules\Usuario\Model\Usuario::class;

    public function definition(): array
    {
        return [
            'nome'    => fake()->name(),
            'email'   => fake()->email(),
            'senha' => '$2y$10$d2GrxVxwPWlHREG7iW5o/eS2eAr4Viz8gOXo.YcXmSMmzM5Y9LMSG', // password
            'status'  => StatusUsuario::ATIVO->value,
        ];
    }
}
