<?php

declare(strict_types=1);

namespace App\Modules\Example\Service;

use App\Modules\Example\Dto\ExampleReadDto;
use App\Modules\Example\Repository\ExampleRepository;

class ExampleService
{
    public function __construct(private readonly ExampleRepository $exampleRepository)
    {
    }

    public function example(ExampleReadDto $dto)
    {
        return $this->exampleRepository->example($dto->asArray());
    }
}
