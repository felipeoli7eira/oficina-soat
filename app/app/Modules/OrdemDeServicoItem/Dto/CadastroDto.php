<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoItem\Dto;

class CadastroDto
{
    public function __construct(
        public string $peca_insumo_uuid,
        public string $os_uuid,
        public string $observacao,
        public int $quantidade,
        public float $valor,
        public bool $excluido = false,
        public ?string $data_exclusao = null
    ) {}

    public function asArray(): array
    {
        return [
            'peca_insumo_uuid' => $this->peca_insumo_uuid,
            'os_uuid'          => $this->os_uuid,
            'observacao'     => $this->observacao,
            'quantidade'     => $this->quantidade,
            'valor'          => $this->valor,
            'excluido'       => $this->excluido,
            'data_exclusao'  => $this->data_exclusao
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}
