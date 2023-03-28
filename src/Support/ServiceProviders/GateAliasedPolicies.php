<?php

namespace Basics\Support\ServiceProviders;

use Illuminate\Support\Facades\Gate;

trait GateAliasedPolicies
{
    /**
     * Define the prefix use to call aliased policies actions.
     */
    protected string $aliasPrefix = 'aliased_';

    /**
     * List the aliased policy actions.
     */
    protected array $aliasedActions = [
        'view', //
        'create',
        'update',
        'delete',
    ];

    /**
     * List the aliased model policies.
     */
    protected array $aliasedPolicies = [
        //
    ];

    /**
     * Append the aliased policies to the gate facade.
     */
    protected function defineAliasedPolicies()
    {
        foreach ($this->aliasedActions as $alias => $policy) {
            foreach ($this->aliasedActions as $action) {
                Gate::define($action . '-' . $alias, [$policy, $this->aliasPrefix . $action]);
            }
        }
    }
}
