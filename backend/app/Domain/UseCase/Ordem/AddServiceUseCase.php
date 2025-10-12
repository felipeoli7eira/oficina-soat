<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Servico\Entidade as Servico;

use App\Infrastructure\Gateway\OrdemGateway;

use DateTimeImmutable;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;

class AddServiceUseCase
{
    public function __construct(public readonly OrdemGateway $ordemGateway, public readonly ServicoGateway $servicoGateway) {}

    public function exec(string $ordemUuid, string $servicoUuid): string
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
            throw new DomainHttpException('Essa ordem não pode mais receber serviços', 400);
        }

        $servico = $this->servicoGateway->encontrarPorIdentificadorUnico($servicoUuid, 'uuid');
        if ($servico instanceof Servico === false) {
            throw new DomainHttpException('Serviço não encontrado na base', 404);
        }

        $uuid = $this->ordemGateway->adicionarServico($ordemUuid, $servicoUuid);

        if (! is_string($uuid)) {
            throw new DomainHttpException('Erro ao adicionar serviço', 500);
        }

        return $uuid;
    }
}
