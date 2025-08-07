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
            'senha' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'status'  => StatusUsuario::ATIVO->value,
        ];
    }
}
