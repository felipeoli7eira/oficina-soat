<?php

declare(strict_types=1);

namespace App\Interface\Gateway;

class UsuarioGateway
{

    public function __construct(public readonly \App\Domain\Usuario\RepositoryContract $repo) {}

    public function read(array $readParams = []): array
    {
        return $this->repo->read($readParams);
    }

    public function create(array $data): array
    {
        return $this->repo->create($data);
    }

    public function findOneBy(string $identifierName, mixed $value): ?array
    {
        return $this->repo->findOneBy(
            $identifierName,
            $value
        );
    }

    public function delete(array $data): bool
    {
        return $this->repo->delete($data);
    }
}
