<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'status'  => 'ativo',
            'role_id' => 1
        ];
    }
}
