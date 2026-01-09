<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Cliente;

use App\Infrastructure\Gateway\ClienteGateway;

class ValidateStatusUseCase
{
    public function __construct(public readonly string $documento) {}

    public function exec(ClienteGateway $gateway): bool
    {
        if (empty($this->documento)) {
            return false;
        }

        $res = $gateway->validaStatus($this->documento);

        return $res;
    }
}
