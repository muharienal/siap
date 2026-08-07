<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EloquentRepositoryInterface
{
  /**
   * Get all models.
   * @param array $columns
   * @param array $relations
   * @return Collection
   */
  public function getAll(array $columns = ['*'], array $relations = []): Collection;

  /**
   * Get all query.
   * @param array $search
   * @param string $skip
   * @param int $limit
   * @param string $sortBy
   * @param string $orderBy
   * @param array $where
   * @param bool $searchOr
   * @return Builder
   */
  public function allQuery(
    $search = [],
    $skip = null,
    $limit = null,
    $sortBy = null,
    $orderBy = null,
    $where = null,
    $searchOr = false
  ): Builder;

  /**
   * Paginate all
   * @param  integer $perPage
   * @param  array   $columns
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator      
   */
  public function paginate($perPage = 15, $columns = ['*']): LengthAwarePaginator;

  /**
   * Find model by id.
   * @param array $id
   * @return Model
   */
  public function findById(
    int $id
  ): ?Model;

  /**
   * Create model
   * @param array $payload
   * @return Model
   */
  public function create(array $payload): ?Model;

  /**
   * Update model
   * @param array $payload
   * @return Model
   */
  public function update(int $modelId, array $payload): ?Model;

  /**
   * Update or create model
   * @param array $match
   * @param array $payload
   * @return Model
   */
  public function updateOrCreate(array $match, array $payload): ?Model;

  /**
   * Delete model
   * @param integer $id
   * @return bool
   */
  public function delete(int $modelId): bool;

  /**
   * Find model by.
   * @param string $column
   * @param string $value
   * @return Model || null
   */
  public function findBy($column, $value): ?Model;
}
