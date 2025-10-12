<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Infrastructure\Gateway\OrdemGateway;

class ReadOneUseCase
{
    public function __construct(public readonly string $uuid) {}

    public function exec(OrdemGateway $gateway): ?array
    {
        if (empty($this->uuid)) {
            return null;
        }

        $entidade = $gateway->encontrarPorIdentificadorUnico($this->uuid, 'uuid');

        return $entidade?->toExternal();
    }
}
