<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Veiculo\Model\Veiculo>
 */
class VeiculoFactory extends Factory
{
    protected $model = \App\Modules\Veiculo\Model\Veiculo::class;

    public function definition(): array
    {
        $marcas = ['Toyota', 'Honda', 'Ford', 'Volkswagen', 'Chevrolet', 'Fiat', 'Hyundai', 'Nissan', 'Renault', 'Peugeot'];
        $modelos = [
            'Toyota' => ['Corolla', 'Camry', 'Hilux', 'Etios', 'Yaris'],
            'Honda' => ['Civic', 'Accord', 'HR-V', 'Fit', 'City'],
            'Ford' => ['Focus', 'Fiesta', 'EcoSport', 'Ka', 'Ranger'],
            'Volkswagen' => ['Golf', 'Polo', 'Jetta', 'Tiguan', 'Gol'],
            'Chevrolet' => ['Onix', 'Cruze', 'S10', 'Tracker', 'Spin'],
            'Fiat' => ['Uno', 'Palio', 'Argo', 'Toro', 'Strada'],
            'Hyundai' => ['HB20', 'Elantra', 'Tucson', 'Creta', 'i30'],
            'Nissan' => ['Versa', 'Sentra', 'March', 'Kicks', 'Frontier'],
            'Renault' => ['Sandero', 'Logan', 'Duster', 'Captur', 'Kwid'],
            'Peugeot' => ['208', '2008', '3008', '308', '207']
        ];

        $cores = ['Branco', 'Prata', 'Preto', 'Azul', 'Vermelho', 'Cinza', 'Bege', 'Verde', 'Amarelo', 'Marrom'];

        $marca = fake()->randomElement($marcas);
        $modelo = fake()->randomElement($modelos[$marca]);

        $placaFormato = fake()->randomElement(['antigo', 'mercosul']);
        if ($placaFormato === 'antigo') {
            $placa = fake()->regexify('[A-Z]{3}-[0-9]{4}');
        } else {
            $placa = fake()->regexify('[A-Z]{3}[0-9][A-Z][0-9]{2}');
        }

        $chassi = fake()->regexify('[A-HJ-NPR-Z0-9]{17}');

        $data = [
            'marca'            => $marca,
            'modelo'           => $modelo,
            'placa'            => $placa,
            'ano_fabricacao'   => fake()->numberBetween(1990, 2024),
            'cor'              => fake()->randomElement($cores),
            'chassi'           => $chassi,
            'excluido'         => fake()->boolean(20),
            'data_cadastro'    => fake()->dateTimeBetween('-2 years', 'now'),
            'data_atualizacao' => null,
        ];

        if ($data['excluido']) {
            $data['data_exclusao'] = fake()->dateTimeBetween($data['data_cadastro'], 'now');
        } else {
            $data['data_exclusao'] = null;
        }

        return $data;
    }
}
