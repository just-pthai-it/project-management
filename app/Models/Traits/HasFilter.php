<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    private array $validOderOptions = ['asc', 'desc'];

    public function scopeFilter (Builder $query, array $parameters)
    {
        foreach ($parameters as $key => $value)
        {
            $method = 'filter' . Str::studly($key);

            if (method_exists($this, $method))
            {
                $this->$method($query, $value);
                continue;
            }

            if ($key == 'sort_by')
            {
                $this->__orderBy($query, $key, $value);
            }
            else
            {
                $this->__where($query, $key, $value);
            }
        }
    }

    private function __where (Builder $query, string $field, string $rawValue) : void
    {
        if ($rawValue == 'all')
        {
            return;
        }

        if (empty($this->filterable))
        {
            return;
        }

        if (key_exists($field, $this->filterable))
        {
            $field = $this->filterable[$field];
        }

        if (in_array($field, $this->filterable))
        {
            $this->__whereIn($query, $field, $rawValue);
            $query->where($field, '=', $rawValue);
        }
    }

    private function __whereIn (Builder $query, string $field, string $value)
    {
        $values = explode('&&', $value);
        if (count($values) > 0)
        {
            $query->whereIn($field, $values);
        }
    }

    private function __orderBy (Builder $query, string $rawValue)
    {
        if (empty($this->sortable))
        {
            return;
        }

        $values = explode(';', $rawValue);
        foreach ($values as $value)
        {
            [$field, $order] = explode(',', $value);

            if (key_exists($field, $this->sortable))
            {
                $field = $this->sortable[$field];
            }

            if (in_array($field, $this->sortable) && in_array($order, $this->validOderOptions))
            {
                $query->orderBy($field, $order);
            }
        }
    }
}