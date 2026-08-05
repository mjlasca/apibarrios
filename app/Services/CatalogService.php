<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Generic CRUD service for catalog entities.
 * Avoids duplicating the same list/store/update/deactivate
 * logic across six separate service classes.
 */
class CatalogService
{
    private const PER_PAGE = 30;

    /**
     * Paginated listing with optional keyword search.
     *
     * @param class-string<Model> $modelClass
     * @param string[] $searchableColumns
     * @param array<string, string> $columnMap  Map of alias => real column for sorting
     */
    public function list(
        string $modelClass,
        array $searchableColumns,
        ?string $keyword = null,
        int $perPage = self::PER_PAGE,
    ): LengthAwarePaginator {
        $query = $modelClass::query()->where('codestado', '1');

        if ($keyword !== null && $keyword !== '') {
            $query->where(function (Builder $q) use ($searchableColumns, $keyword) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            });
        }

        return $query
            ->orderByDesc('ultmod')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Find a single record by its primary key.
     *
     * @param class-string<Model> $modelClass
     */
    public function find(string $modelClass, int $id): Model
    {
        return $modelClass::query()->findOrFail($id);
    }

    /**
     * Create a new record, setting audit fields.
     *
     * @param class-string<Model> $modelClass
     * @param array<string, mixed> $attributes
     */
    public function create(string $modelClass, array $attributes): Model
    {
        $attributes['ultmod'] = now();
        $attributes['user_edit'] = auth()->user()->name ?? 'system';
        $attributes['codestado'] = '1';

        /** @var Model $record */
        $record = new $modelClass();
        $record->fill($attributes);
        $record->save();

        return $record;
    }

    /**
     * Update an existing record, incrementing version when present.
     *
     * @param Model $record
     * @param array<string, mixed> $attributes
     */
    public function update(Model $record, array $attributes): Model
    {
        $attributes['ultmod'] = now();
        $attributes['user_edit'] = auth()->user()->name ?? 'system';

        if (in_array('version', $record->getFillable(), true)) {
            $attributes['version'] = (int) ($record->version ?? 0) + 1;
        }

        $record->fill($attributes);
        $record->save();

        return $record;
    }

    /**
     * Soft-deactivate a record (codestado = 0).
     *
     * @param Model $record
     */
    public function deactivate(Model $record): Model
    {
        $record->codestado = '0';
        $record->ultmod = now();
        $record->user_edit = auth()->user()->name ?? 'system';
        $record->save();

        return $record;
    }
}
