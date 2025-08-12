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

        $empresa = $faker->boolean(30);

        $telefone = '11' . $faker->randomNumber(9, true);
        $telefone = substr($telefone, 0, 11);

        $data = [
            'nome'             => substr($faker->name(), 0, 100),
            'cpf'              => $empresa ? null : $faker->cpf(false),
            'cnpj'             => $empresa ? $faker->cnpj(false) : null,
            'email'            => $faker->email(),
            'telefone_movel'   => $telefone,

            'cep'              => substr(str_replace('-', '', $faker->postcode()), 0, 8),
            'logradouro'       => substr($faker->streetName(), 0, 100),
            'numero'           => substr($faker->buildingNumber(), 0, 20),
            'bairro'           => substr($faker->citySuffix(), 0, 50),
            'complemento'      => $faker->optional(0.3)->text(100),
            'cidade'           => substr($faker->city(), 0, 50),
            'uf'               => $faker->stateAbbr(),

            'excluido'         => false,
            'data_cadastro'    => now(),
            'data_atualizacao' => now(),
        ];

        return $data;
    }
}
