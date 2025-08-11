<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\OrdemDeServico\Model\OrdemDeServico>
 */
class OrdemDeServicoFactory extends Factory
{
    protected $model = \App\Modules\OrdemDeServico\Model\OrdemDeServico::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('pt_BR');

        $descricoes = [
            'Troca de óleo e filtros',
            'Revisão completa de freios',
            'Alinhamento e balanceamento',
            'Diagnóstico eletrônico',
            'Manutenção do ar condicionado',
            'Troca de pastilhas de freio',
            'Revisão da suspensão',
            'Manutenção preventiva',
            'Reparo no sistema elétrico',
            'Troca de correias e mangueiras',
            'Limpeza do sistema de injeção',
            'Revisão do motor'
        ];

        $dataAbertura = $faker->dateTimeBetween('-6 months', 'now');
        $valorTotal = $faker->randomFloat(2, 150, 2500);
        $valorDesconto = $faker->randomFloat(2, 0, $valorTotal * 0.2);

        $jaFinalizada = $faker->boolean(20);
        $dataFinalizacao = null;

        if ($jaFinalizada) {
            $dataFinalizacao = $faker->dateTimeBetween($dataAbertura, 'now');
        }

        $data = [
            'data_abertura'         => $dataAbertura,
            'data_finalizacao'      => $dataFinalizacao,
            'prazo_validade'        => $faker->numberBetween(7, 90), // Prazo em dias
            'cliente_id'            => \App\Modules\Cliente\Model\Cliente::factory(),
            'veiculo_id'            => \App\Modules\Veiculo\Model\Veiculo::factory(),
            'descricao'             => $faker->randomElement($descricoes),
            'valor_desconto'        => $valorDesconto,
            'valor_total'           => $valorTotal,
            'usuario_id_atendente'  => \App\Modules\Usuario\Model\Usuario::factory(),
            'usuario_id_mecanico'   => \App\Modules\Usuario\Model\Usuario::factory(),
            'excluido'              => false,
            'data_exclusao'         => null,
            'data_cadastro'         => $dataAbertura,
            'data_atualizacao'      => $faker->optional(0.6)->dateTimeBetween($dataAbertura, 'now'),
        ];

        if ($data['excluido']) {
            $data['data_exclusao'] = $faker->dateTimeBetween($data['data_atualizacao'] ?? $data['data_cadastro'], 'now');
        }

        return $data;
    }

    /**
     * Indica que a ordem de serviço está finalizada
     */
    public function finalizada()
    {
        return $this->state(function (array $attributes) {
            $dataAbertura = $attributes['data_abertura'] ?? now()->subDays(rand(1, 30));
            return [
                'data_finalizacao' => $this->faker->dateTimeBetween($dataAbertura, 'now'),
            ];
        });
    }

    /**
     * Indica que a ordem de serviço está em andamento (não finalizada)
     */
    public function emAndamento()
    {
        return $this->state(function (array $attributes) {
            return [
                'data_finalizacao' => null,
            ];
        });
    }

    /**
     * Indica que a ordem de serviço foi excluída
     */
    public function excluida()
    {
        return $this->state(function (array $attributes) {
            $dataBase = $attributes['data_atualizacao'] ?? $attributes['data_cadastro'] ?? now()->subDays(5);
            return [
                'excluido' => true,
                'data_exclusao' => $this->faker->dateTimeBetween($dataBase, 'now'),
            ];
        });
    }
}
