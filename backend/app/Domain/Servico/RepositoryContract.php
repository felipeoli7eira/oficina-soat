<?php

namespace App\Domain\Servico;

interface RepositoryContract
{
    public function read(): array;
    public function create(array $data): array;
    public function findOneBy(string $identifierName, mixed $value, ?array $where = []): ?array;
    public function delete(array $deletedData): bool;
    public function update(string $uuid, array $novosDados): int;
}
