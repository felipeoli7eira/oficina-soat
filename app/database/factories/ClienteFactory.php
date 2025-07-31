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
        $faker = \Faker\Factory::create('pt_BR');

        $empresa = $faker->boolean(30); // 30% chance de ser empresa

        $data = [
            'nome'             => $faker->name(),
            'cpf'              => $empresa ? null : $faker->cpf(false),
            'cnpj'             => $empresa ? $faker->cnpj(false) : null,
            'email'            => $faker->email(),
            'telefone_movel'   => str_replace(['(', ')', '-', ' '], '', $faker->phoneNumber()),

            'cep'              => str_replace('-', '', $faker->postcode()),
            'logradouro'       => $faker->streetName(),
            'numero'           => $faker->buildingNumber(),
            'bairro'           => $faker->citySuffix(),
            'complemento'      => $faker->optional(0.3)->secondaryAddress(), // nem todo mundo tem
            'cidade'           => $faker->city(),
            'uf'               => $faker->stateAbbr(),

            'excluido'         => $faker->boolean(10), // só 10% excluídos
            'data_cadastro'    => $faker->dateTimeBetween('-1 year', 'now'),
            'data_atualizacao' => $faker->dateTimeBetween('-1 year', 'now'),
        ];

        if ($data['excluido']) {
            $data['data_exclusao'] = $faker->dateTimeBetween($data['data_atualizacao'], 'now');
        }

        return $data;
    }
}
