<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\PecaInsumo\Model\PecaInsumo>
 */
class PecaInsumoFactory extends Factory
{
    protected $model = \App\Modules\PecaInsumo\Model\PecaInsumo::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        $pecasAutomotivas = [
            'Filtro de Óleo',
            'Filtro de Ar',
            'Filtro de Combustível',
            'Vela de Ignição',
            'Pastilha de Freio',
            'Disco de Freio',
            'Correia Dentada',
            'Amortecedor Dianteiro',
            'Amortecedor Traseiro',
            'Pneu 175/70 R13',
            'Pneu 185/60 R15',
            'Bateria 60Ah',
            'Alternador',
            'Motor de Arranque',
            'Radiador',
            'Bomba d\'Água',
            'Termostato',
            'Junta do Cabeçote',
            'Óleo Motor 5W30',
            'Óleo de Freio DOT4',
            'Fluido de Arrefecimento',
            'Lâmpada H4',
            'Lâmpada H7',
            'Fusível 10A',
            'Fusível 15A',
            'Cabo de Vela',
            'Rolamento de Roda',
            'Bucha da Bandeja',
            'Pivô da Suspensão',
            'Coxim do Motor'
        ];

        $statusDisponiveis = ['ativo', 'inativo', 'descontinuado', 'em_falta'];

        $gtin = $faker->numerify('789#########');

        $descricao = $faker->randomElement($pecasAutomotivas);

        $valorCusto = $faker->randomFloat(2, 5.00, 500.00);
        $valorVenda = $valorCusto * $faker->randomFloat(2, 1.3, 2.5);

        $qtdAtual = $faker->numberBetween(0, 200);
        $qtdSegregada = $faker->numberBetween(0, min(10, $qtdAtual));

        $data = [
            'gtin'             => $gtin,
            'descricao'        => $descricao,
            'valor_custo'      => $valorCusto,
            'valor_venda'      => round($valorVenda, 2),
            'qtd_atual'        => $qtdAtual,
            'qtd_segregada'    => $qtdSegregada,
            'status'           => $faker->randomElement($statusDisponiveis),
            'excluido'         => $faker->boolean(20), // 20% de chance de estar excluído
            'data_cadastro'    => $faker->dateTimeBetween('-2 years', 'now'),
            'data_atualizacao' => null,
        ];

        if ($data['excluido']) {
            $data['data_exclusao'] = $faker->dateTimeBetween($data['data_cadastro'], 'now');
        } else {
            $data['data_exclusao'] = null;
        }

        return $data;
    }
}
