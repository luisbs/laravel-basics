<?php

namespace Basics\Support\Database;

use Illuminate\Support\Collection;

trait QueryPlucking
{
    /**
     * Get a disposable instance of the query.
     *
     * @return  null|\Illuminate\Database\Query\Builder  the class where the methods exists.
     */
    abstract protected function getQuery(): ?\Illuminate\Database\Eloquent\Builder;

    /**
     * Direction to use on grouped methods.
     */
    protected string $orderDirection = 'DESC';

    /**
     * Column name to use on Date related methods.
     */
    protected string $createdAtColumn = 'created_at';

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
     * @return \Illuminate\Support\Collection
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
        if (!is_null($query = $this->getQuery())) {
            // SELECT COUNT(*) AS total, <label-function> AS label
            // FROM <tablename>
            // GROUP BY label
            // ORDER BY label DESC
            return $query->selectRaw("COUNT(*) AS total, $labelFn AS label")
                ->groupBy('label')
                ->orderBy('label', $this->orderDirection)
                ->pluck('total', 'label');
        }

        return Collection::make();
    }
}
