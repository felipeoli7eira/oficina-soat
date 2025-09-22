<?php

declare(strict_types=1);

namespace App\Interface\Apresentacao;

use App\Contrato\Apresentacao;

final class Json implements Apresentacao
{
    public function __construct(public readonly array $dados)
    {
    }

    #[\Override]
    public function apresentar(): void
    {
        response()->json($this->dados)->send();
    }
}
