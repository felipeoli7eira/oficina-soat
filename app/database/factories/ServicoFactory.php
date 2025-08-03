<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Servico\Model\Servico>
 */
class ServicoFactory extends Factory
{
    protected $model = \App\Modules\Servico\Model\Servico::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');
        $status = ['ATIVO', 'INATIVO'];

        $servicos = [
            'Troca de Óleo',
            'Alinhamento',
            'Balanceamento',
            'Revisão Geral',
            'Troca de Pastilhas',
            'Lubrificação Suspensão',
            'Diagnóstico Eletrônico',
            'Manutenção de Ar Condicionado',
            'Pintura',
            'Polimento'
        ];

        $data = [
            'descricao'        => $faker->unique()->randomElement($servicos),
            'valor'            => $faker->randomFloat(0, 100, 1000),
            'status'           => $faker->randomElement($status),
            'excluido'         => false, // atenção ao testes
            'data_cadastro'    => $faker->dateTimeBetween('-1 year', 'now'),
            'data_atualizacao' => $faker->dateTimeBetween('-1 year', 'now'),
        ];

        if ($data['excluido']) {
            $data['data_exclusao'] = $faker->dateTimeBetween($data['data_atualizacao'], 'now');
        }

        return $data;
    }
}
