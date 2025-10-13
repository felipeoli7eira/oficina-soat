<?php

declare(strict_types=1);

namespace App\Infrastructure\Presenter;

use App\Signature\PresenterInterface;

final class HttpJsonPresenter implements PresenterInterface
{
    private int $statusCode = 200;

    #[\Override]
    public function toPresent(array $dados): mixed
    {
        return response()->json($dados, $this->statusCode);
    }

    public function setStatusCode(int $statusCode): HttpjsonPresenter
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
