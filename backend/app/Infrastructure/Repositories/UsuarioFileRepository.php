<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Facades\Storage;

class UsuarioFileRepository implements \App\Domain\Usuario\RepositoryContract
{
    public function __construct()
    {
        $this->createIfNotExists('database/database.json');
    }

    private function createIfNotExists(string $filename)
    {
        if (Storage::disk('local')->exists($filename) === false) {
            Storage::disk('local')->put($filename, '{}');
        }
    }

    public function read(array $readParams = []): array
    {
        $data = Storage::json('database/database.json');

        if (! array_key_exists('usuarios', $data)) {
            return [];
        }

        return array_reverse($data['usuarios'], false);
    }

    public function create(array $data): array
    {
        $database = Storage::disk('local')->get('database/database.json');

        $database = json_decode($database, true);

        if (! array_key_exists('usuarios', $database)) {
            $database['usuarios'] = [];
        }

        if (!isset($data['uuid']) || empty($data['uuid'])) {
            $data['uuid'] = $this->uuid();
        }

        $database['usuarios'][] = $data;

        Storage::disk('local')->put('database/database.json', json_encode($database));

        return $data;
    }

    public function uuid(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }

    public function findOneBy(string $identifierName, mixed $value): ?array
    {
        $database = Storage::disk('local')->get('database/database.json');

        $database = json_decode($database, true);

        if (!isset($database['usuarios']) || !is_array($database['usuarios'])) {
            return null;
        }

        $data = array_filter($database['usuarios'], fn ($u) => $u[$identifierName] === $value);

        if (empty($data)) {
            return null;
        }

        return current($data);
    }
}
