<?php

namespace Basics\Support\Query;

use Illuminate\Support\Collection;

trait QueryPlucking
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
     * Direction to use on grouped methods.
     */
    protected string $orderDirection = 'DESC';

    /**
     * Column name to use on Date related methods.
     */
    protected string $createdAtColumn = 'created_at';

    /**
     * Retrieve an array counting groups of records _createdAt_ by **year**.
     *
     * @return \Illuminate\Support\Collection example `['2015' => 13]` from `'2015-07-23'`.
     */
    public function pluckByYear(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%Y', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **year-month**.
     *
     * @return \Illuminate\Support\Collection example `['2015-07' => 13]` from `'2015-07-23'`.
     */
    public function pluckByYearMonth(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%Y-%m', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **year-month-day**.
     *
     * @return \Illuminate\Support\Collection example `['2015-07-23' => 13]` from `'2015-07-23'`.
     */
    public function pluckByYearMonthDay(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%Y-%m-%d', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **month**.
     *
     * @return \Illuminate\Support\Collection example `['07' => 13]` from `'2015-07-23'`.
     */
    public function pluckByMonth(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%m', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **month-day**.
     *
     * @return \Illuminate\Support\Collection example `['07-23' => 13]` from `'2015-07-23'`.
     */
    public function pluckByMonthDay(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%m-%d', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **day**.
     *
     * @return \Illuminate\Support\Collection example `['23' => 13]` from `'2015-07-23'`.
     */
    public function pluckByDay(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%d', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **weekday**.
     *
     * @return \Illuminate\Support\Collection example `['0' => 13]` where `Sunday=0` and `Saturday=6`.
     */
    public function pluckByWeekday(string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckByDateFormat('%w', $aggregator);
    }

    /**
     * Retrieve an array counting groups of records _createdAt_ by **$format**.
     * @see https://www.w3schools.com/sql/func_mysql_date_format.asp
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluckByDateFormat(string $format, string $aggregator = 'count(*)'): Collection
    {
        return $this->pluckBy('DATE_FORMAT('. $this->createdAtColumn .', \''. $format .'\')', $aggregator);
    }

    /**
     * Constructs the select query to perform the pluck.
     *
     * @param  string  $labelFn  any _sql value_ to group the query result by.
     */
    public function pluckBy(string $labelFn, string $aggregator = 'count(*)'): Collection
    {
        if (!$this->hasQuery()) {
            return Collection::make();
        }

        // SELECT COUNT(*) AS total, <label-function> AS label
        // FROM <tablename>
        // GROUP BY label
        // ORDER BY label DESC
        return $this->getQuery()->selectRaw("$aggregator AS total, $labelFn AS label")
            ->groupBy('label')
            ->orderBy('label', $this->orderDirection)
            ->pluck('total', 'label');
    }

    /**
     * Get an array with the values of a given column.
     */
    public function pluck(string $column, ?string $key = null): Collection
    {
        if (!$this->hasQuery()) {
            return Collection::make();
        }

        return $this->getQuery()->pluck($column, $key);
    }
}
