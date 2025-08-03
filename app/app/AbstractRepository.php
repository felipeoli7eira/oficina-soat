<?php

namespace App;

use App\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class AbstractRepository implements RepositoryInterface
{
    protected static string $model;

    public static function model(): Model
    {
        return app(static::$model);
    }

    public static function read(int $perPage = 10, array $columns = ['*'], array $relations = []): ResourceCollection|LengthAwarePaginator
    {
        return self::model()::query()->with($relations)
                                     ->where('excluido', false)
                                     ->paginate($perPage, $columns);
    }

    public static function findOne(int $identifier, array $columns = ['*'], array $relations = []): Model
    {
        return self::model()::query()->with($relations)->find($identifier, $columns);
    }

    public static function create(array $attributes): Model
    {
        return self::model()::query()->create($attributes);
    }

    public static function createOrFirst(array $attributes): Model
    {
        return self::model()::query()->createOrFirst($attributes);
    }

    public static function delete(int $identifier): int
    {
        return self::model()::query()->where(['id' => $identifier])->delete();
    }

    public static function update(int $identifier, array $attributes): int
    {
        $model = self::model()::find($identifier);

        if (! $model) return 0;

        return self::model()::query()->where(['id' => $identifier])->update($attributes);
    }

    public static function updateAndGet(int $identifier, array $attributes): ?Model
    {
        $updated = self::update($identifier, $attributes);

        return $updated ? self::model()::find($identifier) : null;
    }
}
