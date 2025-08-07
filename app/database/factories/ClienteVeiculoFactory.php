<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\ClienteVeiculo\Model\ClienteVeiculo>
 */
class ClienteVeiculoFactory extends Factory
{
    protected $model = \App\Modules\ClienteVeiculo\Model\ClienteVeiculo::class;

    public function definition(): array
    {
        $data = [
            'cliente_id'       => \App\Modules\Cliente\Model\Cliente::factory(),
            'veiculo_id'       => \App\Modules\Veiculo\Model\Veiculo::factory(),
        ];

        return $data;
    }
}
