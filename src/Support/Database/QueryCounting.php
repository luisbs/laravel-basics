<?php

namespace Basics\Support\Models;

use Illuminate\Support\Arr;

/**
 * @see https://laravel.com/docs/7.x/queries#aggregates
 * @see https://github.com/laravel/framework/blob/7.x/src/Illuminate/Database/Query/Builder.php#L2606
 */
trait QueryCounting
{
    /**
     * Get a disposable instance of the query.
     *
     * @return  null|\Illuminate\Database\Query\Builder  the class where the methods exists.
     */
    abstract protected function getQuery(): ?\Illuminate\Database\Eloquent\Builder;

    /**
     * Retrieve a count of the number of rows requested.
     *
     * @param  string|array  $columns
     */
    public function count($columns = '*'): int
    {
        return (int) $this->aggregate(__FUNCTION__, Arr::wrap($columns));
    }

    /**
     * Retrieve the minimum value of a given column.
     */
    public function min(string $column): mixed
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the maximum value of a given column.
     */
    public function max(string $column): mixed
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the sum of the values of a given column.
     */
    public function sum(string $column): mixed
    {
        $result = $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the average of the values of a given column.
     */
    public function avg(string $column): mixed
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Alias for the "avg" method.
     */
    public function average(string $column): mixed
    {
        return $this->avg($column);
    }

    /**
     * Retrive the result of an aggregate function on the database.
     *
     * @param  string|array  $columns
     *
     * @see https://mariadb.com/kb/en/aggregate-functions/
     * @see https://dev.mysql.com/doc/refman/8.0/en/aggregate-functions.html
     * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
     * @see https://learn.microsoft.com/en-us/sql/t-sql/functions/aggregate-functions-transact-sql
     */
    public function aggregate(string $function, $columns = ['*']): mixed
    {
        if (!is_null($query = $this->getQuery())) {
            return $query->aggregate($function, $columns);
        }

        return NULL;
    }

    /**
     * Retrive the result of a numeric aggregate function on the database.
     *
     * @param  string|array  $columns
     * @return int|float
     */
    public function numericAggregate(string $function, $columns = ['*']): mixed
    {
        if (!is_null($query = $this->getQuery())) {
            return $query->numericAggregate($function, $columns);
        }

        return 0;
    }
}
