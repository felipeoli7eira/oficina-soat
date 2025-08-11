<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoServico\Dto;

class CadastroDto
{
    public function __construct(
        public string $servico_uuid,
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
            'servico_uuid' => $this->servico_uuid,
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
