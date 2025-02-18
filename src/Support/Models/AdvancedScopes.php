<?php

namespace Basics\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method \Illuminate\Database\Query\Builder  withModelKey($value, ?string $column)
 * @method \Illuminate\Database\Query\Builder  whereUnlessNull(string $column, ?$value)
 * @method \Illuminate\Database\Query\Builder  timespan(?array $values, ?string $column)
 * @method \Illuminate\Database\Query\Builder  after($value, ?string $column)
 * @method \Illuminate\Database\Query\Builder  before($value, ?string $column)
 * @method \Illuminate\Database\Query\Builder  joinParent(string $related, ?string $foreignKey, ?string $localKey)
 * @method \Illuminate\Database\Query\Builder  joinChildren(string $related, ?string $foreignKey, ?string $localKey)
 * @method \Illuminate\Database\Query\Builder  joinTable($related, string $firstKey, string $secondKey, string $type = 'inner')
 */
trait AdvancedScopes
{
    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public abstract function qualifyColumn($column);

    /**
     * Add where filter that accepts a model or a model_key.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model|string|int  $value
     * @return \Illuminate\Database\Query\Builder
     */
    protected function scopeWithModelKey($query, $value, ?string $column = null)
    {
        if (is_null($column)) {
            $column = $query->getModel()->getKeyName();
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
    protected function scopeWhereUnlessNull($query, ?string $column, $value = null)
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
    public function scopeTimespan($query, ?array $values = null, ?string $column = null)
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

        $column = $column ?: $query->getModel()->getCreatedAtColumn();

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
    public function scopeAfter($query, $value = null, ?string $column = null)
    {
        if (is_null($value)) {
            return $query;
        }

        $column = $column ?: $query->getModel()->getCreatedAtColumn();

        return $query->where($this->qualifyColumn($column), '>', $value ?? today());
    }

    /**
     * Filter models before a date.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Illuminate\Support\Carbon  $value
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeBefore($query, $value = null, ?string $column = null)
    {
        if (is_null($value)) {
            return $query;
        }

        $column = $column ?: $query->getModel()->getCreatedAtColumn();

        return $query->where($this->qualifyColumn($column), '<', $value ?? today());
    }

    /**
     * Add a join clause to the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeJoinParent(
        $query,
        string $related,
        ?string $foreignKey = null,
        ?string $localKey = null,
        ?string $alias
    ) {
        $parent = static::instantiateModel($related);

        $alias = $alias ?: $parent->getTable();
        $localKey = $localKey ?: $parent->getKeyName();
        $foreignKey = $foreignKey ?: Str::singular($alias) . '_' . $parent->getKeyName();

        return $this->scopeJoinTable($query, $parent, $localKey, $foreignKey, $alias);
    }

    /**
     * Add a join clause to the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeJoinChildren(
        $query,
        string $related,
        ?string $foreignKey = null,
        ?string $localKey = null,
        ?string $alias
    ) {
        $children = static::instantiateModel($related);
        $parent = $query->getModel();

        $alias = $alias ?: $parent->getTable();
        $localKey = $localKey ?: $parent->getKeyName();
        $foreignKey = $foreignKey ?: Str::singular($alias) . '_' . $parent->getKeyName();

        return $this->scopeJoinTable($query, $children, $foreignKey, $localKey, $alias);
    }

    /**
     * Add a join clause to the query.
     *
     * @param  string|\Basics\Support\Models\Model  $related
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeJoinTable(
        $query,
        $related,
        string $firstKey,
        string $secondKey,
        string $alias,
        string $type = 'inner'
    ) {
        $related = static::instantiateModel($related);

        return $query->join(
            $related->getTable() . ($alias ? ' as '. $alias : ''),
            $related->qualifyColumn($firstKey),
            '=',
            $this->qualifyColumn($secondKey),
            $type,
        );
    }

    /**
     * Creates a new Model instance.
     *
     * @param  string|\Basics\Support\Models\Model  $related
     */
    protected static function instantiateModel($related): Model
    {
        if ($related instanceof Model) {
            return $related;
        }

        $instance = new $related();

        if ($instance instanceof Model) {
            return $instance;
        }

        throw new \Exception("\$related expected to extend AdvancedScopes trait");
    }
}
