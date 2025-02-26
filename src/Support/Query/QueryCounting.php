<?php

namespace Basics\Support\Query;

use Illuminate\Support\Arr;

/**
 * @see https://laravel.com/docs/7.x/queries#aggregates
 * @see https://github.com/laravel/framework/blob/7.x/src/Illuminate/Database/Query/Builder.php#L2606
 */
trait QueryCounting
{
    /**
     * Check if the instance has a valid `$query` object
     */
    abstract protected function hasQuery(): bool;

    /**
     * Get a disposable instance of the query.
     */
    abstract protected function getQuery(): ?\Illuminate\Database\Query\Builder;

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
     *
     * @return mixed
     */
    public function min(string $column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @return mixed
     */
    public function max(string $column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @return mixed
     */
    public function sum(string $column)
    {
        $result = $this->aggregate(__FUNCTION__, [$column]);

        return $result ?: 0;
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @return mixed
     */
    public function avg(string $column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Alias for the "avg" method.
     *
     * @return mixed
     */
    public function average(string $column)
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
     *
     * @return mixed
     */
    public function aggregate(string $function, $columns = ['*'])
    {
        if (!$this->hasQuery()) {
            return NULL;
        }

        return $this->getQuery()->aggregate($function, $columns);
    }

    /**
     * Retrive the result of a numeric aggregate function on the database.
     *
     * @param  string|array  $columns
     * @return int|float
     *
     * @return mixed
     */
    public function numericAggregate(string $function, $columns = ['*'])
    {
        if (!$this->hasQuery()) {
            return 0;
        }

        return $this->getQuery()->numericAggregate($function, $columns);
    }
}
