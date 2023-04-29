<?php

namespace App\Repositories\Contracts;

use App\Helpers\Constants;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface IBaseRepository
{
    public function all (array $columns = ['*']);

    public function insertGetObject (array $data) : Model;

    public function insert (array $data) : void;

    public function insertMultiple (array $data) : void;

    public function update (array $data, array $conditions = [], array $extraMethods = []) : void;

    public function updateById ($id, array $data) : void;

    public function updateByIds (array $ids, array $data) : void;

    public function upsert (array $data, array $uniqueColumns = [], array $updateColumns = []) : void;

    public function updateOrInsert (array $conditions = [], array $data = []) : void;

    public function updateOrCreate (array $conditions = [], array $data = []) : Model;

    public function increment (string $column, array $conditions = [], int $value = 1,
                               array  $extraMethods = []) : void;

    public function incrementByIds (string $column, array $ids, int $value = 1,
                                    array  $extraMethods = []) : void;

    public function count (array $conditions = []) : int;

    public function checkIfExistById (int|string $id) : bool;

    public function checkIfExist (array $conditions, array $extraMethods = []) : bool;

    public function find (array $columns = ['*'], array $conditions = [], array $orders = [],
                          array $limitOffset = [], array $extraMethods = [],
                          array $postMethods = []) : Collection;

    public function findOne (array $columns = ['*'], array $conditions = [], array $orders = [],
                             array $limitOffset = [], array $extraMethods = [],
                             array $postMethods = []) : ?Model;

    public function findByIds (array $ids, array $columns = ['*'], array $orders = [],
                               array $limitOffset = [], array $postMethods = []) : Collection;

    public function findById (int $id, array $columns = ['*'], array $extraMethods = [], array $postMethods = []) : ?Model;

    public function chunk (int $recordsPerChunk, Closure $closure, array $columns = ['*'], array $conditions = [], array $extraMethods = []) : void;

    public function paginate (int   $perPage, array $columns = ['*'], array $conditions = [], array $orders = [],
                              array $extraMethods = []) : LengthAwarePaginator;

    public function delete (array $conditions = [], array $Scopes = []) : void;

    public function deleteById (int|string $id) : void;

    public function deleteByIds (array $ids) : void;

    public function forceDelete (array $conditions = [], array $Scopes = []) : void;

    public function forceDeleteById (int|string $id) : void;

    public function forceDeleteByIds (array $ids) : void;

    public function attachPivots (int|string $id, string $relationship, array $pivots) : void;

    public function detachPivots (int|string $id, string $relationship, array $pivots) : void;

    public function syncPivots (int|string $id, string $relationship, array $pivots) : void;

    public function syncWithoutDetachingPivots (int|string $id, string $relationship, array $pivots) : void;

    public function updateExistingPivotPivot (int|string $id, string $relationship, int|string $pivotId, array $data) : void;

}