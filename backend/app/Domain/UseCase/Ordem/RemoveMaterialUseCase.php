<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Servico\Entidade as Servico;

use App\Infrastructure\Gateway\OrdemGateway;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;

class removeMaterialUseCase
{
    public function __construct(public readonly OrdemGateway $ordemGateway, public readonly MaterialGateway $materialGateway) {}

    public function exec(string $ordemUuid, string $materialUuid): int
    {
        $ordem = $this->ordemGateway->encontrarPorIdentificadorUnico($ordemUuid, 'uuid');
        if ($ordem instanceof Ordem === false) {
            throw new DomainHttpException('Ordem não existe', 404);
        }

        if (! in_array($ordem->status, [
            Ordem::STATUS_RECEBIDA,
            Ordem::STATUS_AGUARDANDO_APROVACAO,
        ])) {
            throw new DomainHttpException('Apenas ordens recebidas ou que estão aguardando aprovação podem ter material removido', 400);
        }

        $res = $this->ordemGateway->removerMaterial($ordemUuid, $materialUuid);

        return $res;
    }
}
