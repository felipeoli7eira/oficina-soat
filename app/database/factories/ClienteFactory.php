<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Cliente\Model\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = \App\Modules\Cliente\Model\Cliente::class;

    public function definition(): array
    {
        $data = [
            'nome'             => fake()->name(),
            'cpf'              => fake()->cpf(false),
            'cnpj'             => fake()->cnpj(false),
            'email'            => fake()->email(),
            'telefone_movel'   => fake()->phoneNumber(),

            'cep'              => str_replace('-', '', fake()->postcode()),
            'logradouro'       => fake()->streetName(),
            'numero'           => fake()->buildingNumber(),
            'bairro'           => fake()->cityPrefix(),
            'complemento'      => fake()->secondaryAddress(),
            'cidade'           => fake()->city(),
            'uf'               => fake()->stateAbbr(),

            'excluido'         => fake()->boolean(),
            'data_cadastro'    => fake()->dateTimeBetween('-1 year', 'now'),
            'data_atualizacao' => fake()->dateTimeBetween('-1 year', 'now'),
        ];

        if ($data['excluido']) {
            $data['data_exclusao'] = fake()->dateTimeBetween('-1 year', 'now');
        }

        return $data;
    }
}
