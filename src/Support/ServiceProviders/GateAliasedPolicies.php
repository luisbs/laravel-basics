<?php

namespace Basics\Support\ServiceProviders;

use Illuminate\Support\Facades\Gate;

trait GateAliasedPolicies
{
    /**
     * Define the prefix use to call aliased policies actions.
     */
    protected function getAliasPrefix()
    {
        return 'aliased_';
    }

    /**
     * List the aliased policy actions.
     */
    protected function getAliasedActions()
    {
        return [
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    /**
     * List the aliased model policies.
     */
    protected function getAliasedPolicies()
    {
        return [];
    }

    /**
     * Append the aliased policies to the gate facade.
     */
    protected function defineAliasedPolicies()
    {
        $prefix = $this->getAliasPrefix();
        $actions = $this->getAliasedActions();
        foreach ($this->getAliasedPolicies() as $alias => $policy) {
            foreach ($actions as $action) {
                Gate::define($action . '-' . $alias, [$policy, $prefix . $action]);
            }
        }
    }
}
