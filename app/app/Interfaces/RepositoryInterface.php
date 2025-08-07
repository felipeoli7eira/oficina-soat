<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    public static function read(int $perPage, array $columns): ResourceCollection|LengthAwarePaginator;

    public static function create(array $attributes): Model;

    public static function createOrFirst(array $attributes): Model;

    public static function findOne(int $identifier, array $columns = ['*']): ?Model;

    public static function delete(int $identifier): int;

    public static function update(int $identifier, array $attributes): int;

    public static function model(): Model;
}
