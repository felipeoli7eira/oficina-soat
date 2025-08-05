<?php

declare(strict_types=1);

namespace App\Modules\OS\Dto;

use Spatie\Permission\Models\Role;

class CadastroDto
{
    public function __construct(
        public string $cliente_uuid,
        public string $veiculo_uuid,
        public string $descricao,
        public float $valor_desconto,
        public float $valor_total,
        public string $usuario_uuid_atendente,
        public string $usuario_uuid_mecanico,
        public int $prazo_validate
    ) {}

    public function asArray(): array
    {
        return [
            'cliente_uuid'             => $this->cliente_uuid,
            'veiculo_uuid'             => $this->veiculo_uuid,
            'descricao'                => $this->descricao,
            'valor_desconto'           => $this->valor_desconto,
            'valor_total'              => $this->valor_total,
            'usuario_uuid_atendente'   => $this->usuario_uuid_atendente,
            'usuario_uuid_mecanico'    => $this->usuario_uuid_mecanico,
            'prazo_validate'           => $this->prazo_validate
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}
