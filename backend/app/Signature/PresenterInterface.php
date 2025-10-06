<?php

namespace App\Signature;

interface PresenterInterface
{
    public function toPresent(array $dados): void;
}
