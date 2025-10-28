<?php

declare(strict_types=1);

namespace App\Domain\Veiculo;

use App\Exception\DomainHttpException;
use DateTime;

final class Entity
{
    public function __construct(
        public string $uuid,
        public string $marca,
        public string $modelo,
        public string $placa,
        public int $ano,
        public string $clienteDonoUuid,

        public DateTime $criadoEm,
        public ?DateTime $atualizadoEm = null,
        public ?DateTime $deletadoEm = null,
    ) {
        $this->validarDados();
    }

    public function validarDados(): void
    {
        $this->garantirNomeValidoDeMarca();
        $this->garantirAnoValido();
        $this->garantirNomeDeModeloValido();
        $this->garantirPlacaValida();
        $this->garantirQueUsuarioDonoFoiInformado();
    }

    public function garantirNomeValidoDeMarca(): void
    {
        if (empty(trim($this->marca))) {
            throw new DomainHttpException('Marca não informada', 400);
        }

        if (strlen($this->marca) > 50) {
            throw new DomainHttpException('Marca não pode ter mais de 50 caracteres', 400);
        }

        if (strlen($this->marca) < 3) {
            throw new DomainHttpException('Marca não pode ter menos de 3 caracteres', 400);
        }
    }

    public function garantirAnoValido(): void
    {
        if ($this->ano < 1900) {
            throw new DomainHttpException('Ano não pode ser menor que 1900', 400);
        }
    }

    public function garantirNomeDeModeloValido(): void
    {
        if (empty(trim($this->modelo))) {
            throw new DomainHttpException('Modelo não informado', 400);
        }

        if (strlen($this->modelo) > 50) {
            throw new DomainHttpException('Modelo não pode ter mais de 50 caracteres', 400);
        }

        if (strlen($this->modelo) < 3) {
            throw new DomainHttpException('Modelo não pode ter menos de 3 caracteres', 400);
        }
    }

    public function garantirPlacaValida(): void
    {
        if (empty(trim($this->placa))) {
            throw new DomainHttpException('Placa não informada', 400);
        }

        if (strlen($this->placa) > 7) {
            throw new DomainHttpException('Placa não pode ter mais de 7 caracteres', 400);
        }

        if (strlen($this->placa) < 4) {
            throw new DomainHttpException('Placa não pode ter menos de 4 caracteres', 400);
        }
    }

    public function garantirQueUsuarioDonoFoiInformado(): void
    {
        if (empty(trim($this->clienteDonoUuid))) {
            throw new DomainHttpException('Cliente dono não informado', 400);
        }
    }

    public function asArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'marca'         => $this->marca,
            'modelo'        => $this->modelo,
            'placa'         => $this->placa,
            'ano'           => $this->ano,
            'cliente_uuid'  => $this->clienteDonoUuid,

            'criado_em'     => $this->criadoEm->format('Y-m-d H:i:s'),
            'deletado_em'   => $this->deletadoEm ? $this->deletadoEm->format('Y-m-d H:i:s') : null,
            'atualizado_em' => $this->atualizadoEm ? $this->atualizadoEm->format('Y-m-d H:i:s') : null,
        ];
    }

    public function toExternal(): array
    {
        return [
            'uuid'          => $this->uuid,
            'marca'         => $this->marca,
            'modelo'        => $this->modelo,
            'placa'         => $this->placa,
            'ano'           => $this->ano,
            'criado_em'     => $this->criadoEm->format('d/m/Y H:i:s'),
        ];
    }

    public function delete(): void
    {
        $this->atualizadoEm = new DateTime();
        $this->deletadoEm   = new DateTime();
    }

    public function update(array $novosDados): void
    {
        $this->atualizadoEm = new DateTime();

        $this->validarEntrada();
    }
}
