<?php

declare(strict_types=1);

namespace App\Interface\Gateway;

use App\Domain\Veiculo\RepositoryContract;

class VeiculoGateway
{

    public function __construct(public readonly RepositoryContract $repo) {}

    public function read(array $readParams = []): array
    {
        return $this->repo->read($readParams);
    }

    public function create(array $data): array
    {
        return $this->repo->create($data);
    }

    public function findOneBy(string $identifierName, mixed $value, ?array $where = []): ?array
    {
        return $this->repo->findOneBy(
            $identifierName,
            $value,
            $where
        );
    }

    public function delete(array $data): bool
    {
        return $this->repo->delete($data);
    }

    public function update(string $uuid, array $data): int
    {
        return $this->repo->update($uuid, $data);
    }
}
