<?php

namespace App\Repositories\Abstracts;

use App\Repositories\Contracts\IBaseRepository;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Container\BindingResolutionException;

abstract class ABaseRepository implements IBaseRepository
{
    /**
     * @var array
     */
    private array $allowedOperators = ['>', '>=', '=', '!=', '<>', '<', '<=', 'like', 'not like', 'in', 'not in', 'null', 'not null'];

    /**
     * @var array
     */
    private array $allowedOrders = ['asc', 'desc'];

    protected Model $model;
    protected $query;

    /**
     * EloquentRepository constructor.
     *
     * @throws BindingResolutionException
     */
    public function __construct ()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name
     * @return string
     */
    abstract function model () : string;

    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function makeModel () : void
    {
        $this->model = app()->make($this->model());
    }

    public function newQuery () : void
    {
        $this->query = $this->model->newQuery();
    }

    public function all (array $columns = ['*'])
    {
        $this->newQuery();
        return $this->query->get($columns);
    }

    public function insertGetObject (array $data) : Model
    {
        $this->newQuery();
        return $this->query->create($data);
    }

    public function insert (array $data) : void
    {
        $this->newQuery();
        $this->query->create($data);
    }

    public function insertMultiple (array $data) : void
    {
        $this->newQuery();
        $this->query->insert($data);
    }

    public function update (array $data, array $conditions = [], array $extraMethods = []) : void
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($extraMethods);
        $this->query->update($data);
    }

    public function updateById ($id, array $data) : void
    {
        $this->newQuery();
        $this->query->find($id)->update($data);
    }

    public function updateByIds (array $ids, array $data) : void
    {
        $this->newQuery();
        $this->query->find($ids)->update($data);
    }

    public function upsert (array $data, array $uniqueColumns = [], array $updateColumns = []) : void
    {
        if (empty($updateColumns))
        {
            $updateColumns['id'] = DB::raw('id');
        }

        $this->newQuery();
        $this->query->upsert($data, $uniqueColumns, $updateColumns);
    }

    public function updateOrInsert (array $conditions = [], array $data = []) : void
    {
        $this->newQuery();
        $this->query->updateOrInsert($conditions, $data);
    }

    public function updateOrCreate (array $conditions = [], array $data = []) : Model
    {
        $this->newQuery();
        return $this->query->updateOrCreate($conditions, $data);
    }

    public function increment (string $column, array $conditions = [], int $value = 1,
                               array  $extraMethods = []) : void
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($extraMethods);
        $this->query->increment($column, $value);
    }

    public function incrementByIds (string $column, array $ids, int $value = 1,
                                    array  $extraMethods = []) : void
    {
        $this->newQuery();
        $this->query->whereIn('id', $ids);
        $this->_addExtraMethod($extraMethods);
        $this->query->increment($column, $value);
    }

    public function count (array $conditions = []) : int
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        return $this->query->count();
    }

    public function checkIfExistById (int|string $id) : bool
    {
        return $this->checkIfExist([['id', '=', $id]]);
    }

    public function checkIfExist (array $conditions, array $extraMethods = []) : bool
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($extraMethods);
        return $this->query->exist();
    }

    public function find (array $columns = ['*'], array $conditions = [], array $orders = [],
                          array $limitOffset = [], array $extraMethods = [],
                          array $postMethods = []) : Collection
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($extraMethods);
        $this->_addOrderBy($orders);
        $this->_addLimitOffset($limitOffset);
        $result = $this->query->get($columns);
        $this->_addPostMethod($result, $postMethods);
        return $result;
    }

    public function findOne (array $columns = ['*'], array $conditions = [], array $orders = [],
                             array $limitOffset = [], array $extraMethods = [],
                             array $postMethods = []) : ?Model
    {
        return $this->find(...func_get_args())[0] ?? null;
    }

    public function findByIds (array $ids, array $columns = ['*'], array $orders = [],
                               array $limitOffset = [], array $postMethods = []) : Collection
    {
        $this->newQuery();
        $this->_addOrderBy($orders);
        $this->_addLimitOffset($limitOffset);
        $result = $this->model->find($ids, $columns);
        $this->_addPostMethod($result, $postMethods);
        return $result;
    }

    public function findById (int $id, array $columns = ['*'], array $extraMethods = [], array $postMethods = []) : ?Model
    {
        $this->newQuery();
        $this->_addExtraMethod($extraMethods);
        $result = $this->query->find($id, $columns);
        $this->_addPostMethod($result, $postMethods);
        return $result;
    }

    public function chunk (int $recordsPerChunk, Closure $closure, array $columns = ['*'], array $conditions = [], array $extraMethods = []) : void
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($extraMethods);
        $this->query->select($columns)->chunk($recordsPerChunk, $closure);
    }

    public function paginate (int   $perPage, array $columns = ['*'], array $conditions = [], array $orders = [],
                              array $extraMethods = []) : LengthAwarePaginator
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addOrderBy($orders);
        $this->_addExtraMethod($extraMethods);
        return $this->query->paginate($perPage, $columns);
    }

    public function delete (array $conditions = [], array $Scopes = []) : void
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($Scopes);
        $this->query->delete();
    }

    public function deleteById (int|string $id) : void
    {
        $this->newQuery();
        $this->query->find($id)->delete();
    }

    public function deleteByIds (array $ids) : void
    {
        $this->newQuery();
        $this->query->wherein('id', $ids)->delete();
    }

    public function forceDelete (array $conditions = [], array $Scopes = []) : void
    {
        $this->newQuery();
        $this->_addWhere($conditions);
        $this->_addExtraMethod($Scopes);
        $this->query->forceDelete();
    }

    public function forceDeleteById (int|string $id) : void
    {
        $this->newQuery();
        $this->query->find($id)->forceDelete();
    }

    public function forceDeleteByIds (array $ids) : void
    {
        $this->newQuery();
        $this->query->wherein('id', $ids)->forceDelete();
    }

    public function attachPivots (int|string $id, string $relationship, array $pivots) : void
    {
        $this->newQuery();
        $this->query->find($id)->{$relationship}()->attach($pivots);
    }

    public function detachPivots (int|string $id, string $relationship, array $pivots) : void
    {
        $this->newQuery();
        $this->query->find($id)->{$relationship}()->detach($pivots);
    }

    public function syncPivots (int|string $id, string $relationship, array $pivots) : void
    {
        $this->newQuery();
        $this->query->find($id)->{$relationship}()->sync($pivots);
    }

    public function syncWithoutDetachingPivots (int|string $id, string $relationship, array $pivots) : void
    {
        $this->newQuery();
        $this->query->find($id)->{$relationship}()->syncWithoutDetaching($pivots);
    }

    public function updateExistingPivotPivot (int|string $id, string $relationship, int|string $pivotId, array $data) : void
    {
        $this->newQuery();
        $this->query->find($id)->{$relationship}()->updateExistingPivot($pivotId, $data);
    }

    protected function _addExtraMethod (array $extraMethods) : void
    {
        if (empty($extraMethods))
        {
            return;
        }

        foreach ($extraMethods as $arr)
        {
            $method      = array_shift($arr);
            $this->query = $this->query->{$method}(...$arr);
        }
    }

    protected function _addWhere (array $conditions = []) : void
    {
        if (empty($conditions))
        {
            return;
        }

        foreach ($conditions as $condition)
        {
            $attribute = $condition[0];
            $operator  = $condition[1];
            $value     = $condition[2] ?? null;

            $method = 'where';
            if (str_contains($operator, '|'))
            {
                $operator = str_replace('|', '', $operator);
                $method   = 'orWhere';
            }

            $this->query = match ($operator)
            {
                'between'  => $this->query->{"{$method}Between"}($attribute),
                'in'       => $this->query->{"{$method}In"}($attribute, $value),
                'not in'   => $this->query->{"{$method}NotIn"}($attribute, $value),
                'null'     => $this->query->{"{$method}Null"}($attribute),
                'not null' => $this->query->{"{$method}NotNull"}($attribute),
                default    => $this->query->{$method}($attribute, $operator, $value),
            };
        }
    }

    protected function _addOrderBy (array $orders = []) : void
    {
        if (empty($orders))
        {
            return;
        }

        foreach ($orders as $order)
        {
            $this->query = $this->query->orderBy(...$order);
        }
    }

    protected function _addLimitOffset (array $pagination) : void
    {
        if (empty($pagination))
        {
            return;
        }

        if (count($pagination) == 1)
        {
            $this->query = $this->query->take($pagination[0]);
        }
        else
        {
            $this->query = $this->query->limit($pagination[0])->offset($pagination[1]);
        }
    }

    protected function _addPostMethod (&$result, array $postMethods) : void
    {
        if (empty($postMethods))
        {
            return;
        }

        foreach ($postMethods as $arr)
        {
            $function = array_shift($arr);
            $result   = $result->$function(...$arr);
        }
    }


    /**
     * @param array $conditions
     *
     * @return boolean
     */
    private function validateCondition (array $conditions = [])
    {
        foreach ($conditions as $condition)
        {
            if (!is_array($condition))
            {
                die("condition error");
            }

            $attribute = $condition[0];
            $operator  = $condition[1];

            if (!in_array($operator, $this->allowedOperators))
            {
                die("condition error");
            }
        }

        return true;
    }

    private function validateOrderBy (array $orderBy = [])
    {
        $check = true;
        if (!$orderBy || !is_array($orderBy))
        {
            $check = false;
        }

        if (!isset($orderBy[0]) || !isset($orderBy[1]))
        {
            $check = false;
        }

        $order = isset($orderBy[1]) ? $orderBy[1] : '';
        if (!in_array($order, $this->allowedOrders))
        {
            $check = false;
        }

        return $check;
    }
}
