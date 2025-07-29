<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Dto;

class CadastroDto
{
    public function __construct(
        public string $marca,
        public string $modelo,
        public int $ano,
        public string $placa,
        public ?string $cor,
        public string $chassi
    ) {}

    public function asArray(): array
    {
        return [
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'ano_fabricacao' => $this->ano,
            'placa' => $this->placa,
            'cor' => $this->cor,
            'chassi' => $this->chassi,
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}
