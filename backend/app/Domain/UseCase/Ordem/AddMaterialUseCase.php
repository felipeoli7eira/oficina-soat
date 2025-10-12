<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Material\Entidade as Material;

use App\Infrastructure\Gateway\OrdemGateway;

use DateTimeImmutable;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;

class AddMaterialUseCase
{
    public function __construct(public readonly OrdemGateway $ordemGateway, public readonly MaterialGateway $materialGateway) {}

    public function exec(string $ordemUuid, string $materialUuid): string
    {
        $ordem = $this->ordemGateway->encontrarPorIdentificadorUnico($ordemUuid, 'uuid');
        if ($ordem instanceof Ordem === false) {
            throw new DomainHttpException('Ordem não existe', 404);
        }

        if (in_array($ordem->status, [
            Ordem::STATUS_FINALIZADA,
            Ordem::STATUS_CANCELADA,
            Ordem::STATUS_REPROVADA,
            Ordem::STATUS_ENTREGUE,
        ])) {
            throw new DomainHttpException('Essa ordem não pode mais receber materiais', 400);
        }

        $material = $this->materialGateway->encontrarPorIdentificadorUnico($materialUuid, 'uuid');
        if ($material instanceof Material === false) {
            throw new DomainHttpException('Material não encontrado', 404);
        }

        $uuid = $this->ordemGateway->adicionarMaterial($ordemUuid, $materialUuid);

        if (! is_string($uuid)) {
            throw new DomainHttpException('Erro ao adicionar material', 500);
        }

        return $uuid;
    }
}
