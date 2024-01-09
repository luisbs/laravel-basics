<?php

namespace Basics\Support\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class QueryCounting
{
    /**
     * Base query to execute.
     */
    protected ?Builder $query;

    /**
     * Direction to use on grouped methods.
     */
    protected string $orderDirection;

    /**
     * Column name to use on Date related methods.
     */
    protected string $createdAtColumn;

    /**
     * Create a new instance of the Query wrapper.
     */
    public function __construct(
        ?Builder $query = null,
        string $orderDirection = 'DESC',
        string $createdAtColumn = null
    ) {
        $model = $query->getModel();

        $this->query = $query;
        $this->orderDirection = $orderDirection;
        $this->createdAtColumn = $this->query->qualifyColumn(
            $createdAtColumn ?? $model->getCreatedAtColumn() ?? 'created_at'
        );
    }

    /**
     * Instantiate a new wrapper.
     */
    public static function make(
        ?Builder $query = null,
        string $orderDirection = 'DESC',
        string $createdAtColumn = null
    ): QueryCounting {
        return new static($query, $orderDirection, $createdAtColumn);
    }

    /**
     * Counting globally.
     *
     * @return int the count method result.
     */
    public function count(): int
    {
        if (is_null($this->query)) {
            return 0;
        }

        return $this->query->count();
    }

    /**
     * Count by **year**.
     *
     * @return \Illuminate\Support\Collection example `['2015' => 13]` from `'2015-07-23'`.
     */
    public function pluckByYear(): Collection
    {
        return $this->pluckByDateFormat('%Y');
    }

    /**
     * Count by **year-month**.
     *
     * @return \Illuminate\Support\Collection example `['2015-07' => 13]` from `'2015-07-23'`.
     */
    public function pluckByYearMonth(): Collection
    {
        return $this->pluckByDateFormat('%Y-%m');
    }

    /**
     * Count by **year-month-day**.
     *
     * @return \Illuminate\Support\Collection example `['2015-07-23' => 13]` from `'2015-07-23'`.
     */
    public function pluckByYearMonthDay(): Collection
    {
        return $this->pluckByDateFormat('%Y-%m-%d');
    }

    /**
     * Count by **month**.
     *
     * @return \Illuminate\Support\Collection example `['07' => 13]` from `'2015-07-23'`.
     */
    public function pluckByMonth(): Collection
    {
        return $this->pluckByDateFormat('%m');
    }

    /**
     * Count by **month-day**.
     *
     * @return \Illuminate\Support\Collection example `['07-23' => 13]` from `'2015-07-23'`.
     */
    public function pluckByMonthDay(): Collection
    {
        return $this->pluckByDateFormat('%m-%d');
    }

    /**
     * Count by **day**.
     *
     * @return \Illuminate\Support\Collection example `['23' => 13]` from `'2015-07-23'`.
     */
    public function pluckByDay(): Collection
    {
        return $this->pluckByDateFormat('%d');
    }

    /**
     * Count by **weekday**.
     *
     * @return \Illuminate\Support\Collection example `['0' => 13]` where `Sunday=0` and `Saturday=6`.
     */
    public function pluckByWeekday(): Collection
    {
        return $this->pluckByDateFormat('%w');
    }

    /**
     * Count groups using a formated date.
     * @see https://www.w3schools.com/sql/func_mysql_date_format.asp
     *
     * @return \Illuminate\Support\Collection example `['0' => 13]` where `Sunday=0` and `Saturday=6`.
     */
    public function pluckByDateFormat(string $format): Collection
    {
        return $this->pluckBy('DATE_FORMAT(' + $this->createdAtColumn + ', \'' + $format + '\')');
    }

    /**
     * Constructs the select query to perform the pluck.
     *
     * @param  string  $labelFn  any _sql value_ to group the query result by.
     */
    public function pluckBy(string $labelFn): Collection
    {
        if (is_null($this->query)) {
            return Collection::make();
        }

        // SELECT COUNT(*) AS total, <label-function> AS label
        // FROM <tablename>
        // GROUP BY label
        // ORDER BY label DESC
        return $this->query->selectRaw("COUNT(*) AS total, $labelFn AS label")
            ->groupBy('label')
            ->orderBy('label', 'DESC')
            ->pluck('total', 'label');
    }
}
