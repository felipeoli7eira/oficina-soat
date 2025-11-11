<?php

declare(strict_types=1);

namespace App\Application\Veiculo;

use App\Domain\Veiculo\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\VeiculoGateway;
use DateTime;
use RuntimeException;
use \App\Exception\DomainHttpException;

final class ReadUseCase
{
    public function __construct(public readonly VeiculoGateway $gateway) {}

    public function handle(array $readParams = []): array
    {
        if ($this->gateway instanceof VeiculoGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $dados = $this->gateway->read($readParams);

        return $dados;
    }
}
