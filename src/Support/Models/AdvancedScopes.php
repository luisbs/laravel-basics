<?php

namespace Basics\Support\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method \Illuminate\Database\Eloquent\Builder  withModelKey($value, string $column = '<PRIMARY_KEY>')
 * @method \Illuminate\Database\Eloquent\Builder  whereUnlessNull(string $column, $value = null)
 * @method \Illuminate\Database\Eloquent\Builder  timespan(array $values, string $column = 'created_at')
 * @method \Illuminate\Database\Eloquent\Builder  after($value, string $column = 'created_at')
 * @method \Illuminate\Database\Eloquent\Builder  before($value, string $column = 'created_at')
 */
trait AdvancedScopes
{
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    abstract public function getTable();

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    abstract public function getKeyName();

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    abstract public function qualifyColumn($column);

    /**
     * Add where filter that accepts a model or a model_key.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model|string|int  $value
     * @return \Illuminate\Database\Query\Builder
     */
    protected function scopeWithModelKey($query, $value, string $column = null)
    {
        if (is_null($column)) {
            $column = $this->getKeyName();
        }

        if ($value instanceof Model) {
            $value = $value->getKey();
        }

        return $query->where($this->qualifyColumn($column), $value);
    }

    /**
     * Add where filter unless the value is null.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Query\Builder
     */
    protected function scopeWhereUnlessNull($query, string $column, $value = null)
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where($this->qualifyColumn($column), $value);
    }

    /**
     * Filter model table by date column.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeTimespan($query, array $values = null, string $column = 'created_at')
    {
        if (is_null($values)) {
            return $query;
        }

        //? can be seen as: ]null, n]
        if (is_null($values[0])) {
            return $this->scopeBefore($query, $values[1], $column);
        }

        //? can be seen as: [n, null[
        if (is_null($values[1])) {
            return $this->scopeAfter($query, $values[0], $column);
        }

        //? can be seen as: [n, m]
        return $query->whereBetween($this->qualifyColumn($column), $values);
    }

    /**
     * Filter models after a date.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Illuminate\Support\Carbon  $value
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAfter($query, $value = null, string $column = 'created_at')
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where($this->qualifyColumn($column), '>', $value ?? today());
    }

    /**
     * Filter models before a date.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Illuminate\Support\Carbon  $value
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeBefore($query, $value = null, string $column = 'created_at')
    {
        if (is_null($value)) {
            return $query;
        }

        return $query->where($this->qualifyColumn($column), '<', $value ?? today());
    }
}
