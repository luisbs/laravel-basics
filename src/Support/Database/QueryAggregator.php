<?php

namespace Basics\Support\Models;

use Basics\Support\Database\QueryPlucking;
use Illuminate\Database\Eloquent\Builder;

class QueryAggregator
{
    use QueryCounting, QueryPlucking;

    /**
     * Base query to execute.
     *
     * @var  null|\Illuminate\Database\Query\Builder  the class where the methods exists.
     */
    protected ?Builder $query = null;

    /**
     * Create a new instance of the Query wrapper.
     */
    public function __construct(
        ?Builder $query = null,
        string $orderDirection = 'DESC',
        string $createdAtColumn = null
    ) {
        if (is_null($query)) {
            return;
        }

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
    ): QueryAggregator {
        return new static($query, $orderDirection, $createdAtColumn);
    }

    /**
     * Get a disposable instance of the query.
     *
     * @return  null|\Illuminate\Database\Query\Builder  the class where the methods exists.
     */
    protected function getQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        if (is_null($this->query)) {
            return NULL;
        }

        // use copies to avoid modifications on the base `$query` object
        // this allows to use multiple QueryAggregator methods
        // with the same base `$query`
        return $this->query->cloneWithout([]);
    }
}
