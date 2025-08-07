<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem>
 */
class OrdemDeServicoItemFactory extends Factory
{
    protected $model = \App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        $observacoes = [
            'Item conforme solicitado pelo cliente',
            'Peça original substituída por compatível',
            'Necessária troca urgente',
            'Item preventivo recomendado',
            'Peça danificada identificada na revisão',
            'Substituição conforme manual do fabricante',
            'Item adicional sugerido pelo mecânico',
            'Peça de qualidade premium solicitada',
            'Substituição devido ao desgaste normal',
            'Item obrigatório para aprovação',
            'Peça importada - prazo diferenciado',
            'Substituição por segurança'
        ];

        $quantidade = $faker->numberBetween(1, 10);
        $valorUnitario = $faker->randomFloat(2, 15, 500);
        $valorTotal = $quantidade * $valorUnitario;

        $dataCadastro = $faker->dateTimeBetween('-6 months', 'now');

        return [
            'peca_insumo_id'    => \App\Modules\PecaInsumo\Model\PecaInsumo::factory(),
            'os_id'             => \App\Modules\OrdemDeServico\Model\OrdemDeServico::factory(),
            'observacao'        => $faker->randomElement($observacoes),
            'quantidade'        => $quantidade,
            'valor'             => $valorTotal,
            'excluido'          => false,
            'data_exclusao'     => null,
            'data_cadastro'     => $dataCadastro,
            'data_atualizacao'  => $faker->optional(0.4)->dateTimeBetween($dataCadastro, 'now'),
        ];
    }

    /**
     * Item para uma OS específica
     */
    public function paraOS($osId)
    {
        return $this->state(function (array $attributes) use ($osId) {
            return [
                'os_id' => $osId,
            ];
        });
    }

    /**
     * Item para uma peça/insumo específico
     */
    public function paraPecaInsumo($pecaInsumoId)
    {
        return $this->state(function (array $attributes) use ($pecaInsumoId) {
            return [
                'peca_insumo_id' => $pecaInsumoId,
            ];
        });
    }
}
