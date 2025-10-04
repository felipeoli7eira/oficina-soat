<?php

declare(strict_types=1);

namespace App\Infrastructure\Presenter;

use App\Signature\PresenterInterface;

final class HttpJsonPresenter implements PresenterInterface
{
    private int $statusCode = 200;

    #[\Override]
    public function toPresent(array $dados): void
    {
        response()->json($dados, $this->statusCode)->send();
    }

    public function setStatusCode(int $statusCode): HttpjsonPresenter
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
