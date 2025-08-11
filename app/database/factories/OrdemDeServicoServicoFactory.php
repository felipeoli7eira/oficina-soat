<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico>
 */
class OrdemDeServicoServicoFactory extends Factory
{
    protected $model = \App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        $observacoes = [
            'Serviço conforme solicitado pelo cliente',
            'Serviço adicional recomendado pelo mecânico',
            'Manutenção preventiva necessária',
            'Serviço de emergência solicitado',
            'Procedimento padrão do fabricante',
            'Serviço especializado requerido',
            'Manutenção corretiva urgente',
            'Serviço de qualidade premium',
            'Procedimento de segurança obrigatório',
            'Serviço conforme manual técnico',
            'Manutenção programada',
            'Serviço complementar sugerido'
        ];

        $quantidade = $faker->numberBetween(1, 5);
        $valorUnitario = $faker->randomFloat(2, 50, 800);
        $valorTotal = $quantidade * $valorUnitario;

        $dataCadastro = $faker->dateTimeBetween('-6 months', 'now');

        return [
            'servico_id'        => \App\Modules\Servico\Model\Servico::factory(),
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
     * Serviço para uma OS específica
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
     * Serviço específico para uma OS
     */
    public function paraServico($servicoId)
    {
        return $this->state(function (array $attributes) use ($servicoId) {
            return [
                'servico_id' => $servicoId,
            ];
        });
    }
}
