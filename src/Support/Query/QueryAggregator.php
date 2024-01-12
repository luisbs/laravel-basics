<?php

namespace Basics\Support\Query;

use Basics\Support\Query\QueryPlucking;
use Illuminate\Database\Query\Builder;

class QueryAggregator
{
    use QueryCounting, QueryPlucking;

    /**
     * Base query to execute.
     */
    protected ?Builder $query;

    /**
     * Create a new instance of the Query wrapper.
     *
     * @param  null|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function __construct(
        $query,
        string $orderDirection = 'DESC',
        string $createdAtColumn = null
    ) {
        if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
            $createdAtColumn = $createdAtColumn ?? $query->getModel()->getCreatedAtColumn();
            $query = $query->getQuery();
        }

        if (!($query instanceof \Illuminate\Database\Query\Builder)){
            throw new \InvalidArgumentException('$query is expected to be Query\Builder or Eloquent\Builder');
        }

        $this->query = $query;
        $this->orderDirection = $orderDirection;
        $this->createdAtColumn = $query->qualifyColumn($createdAtColumn ?? 'created_at');
    }

    /**
     * Instantiate a new wrapper.
     *
     * @param  null|\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function make(
        $query,
        string $orderDirection = 'DESC',
        string $createdAtColumn = null
    ): QueryAggregator {
        return new static($query, $orderDirection, $createdAtColumn);
    }

    /**
     * Check if the instance has a valid `$query` object
     */
    protected function hasQuery(): bool
    {
        return !is_null($this->query);
    }

    /**
     * Get a disposable instance of the query.
     */
    protected function getQuery(): ?\Illuminate\Database\Query\Builder
    {
        if ($this->hasQuery()) {
            // use copies to avoid modifications on the base `$query` object
            // this allows to use multiple QueryAggregator methods
            // with the same base `$query`
            return $this->query->cloneWithout([]);
        }

        throw new \Exception('Unexpected situation on QueryAggregator');
    }
}
