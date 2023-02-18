<?php

namespace Basics\Account;

trait HasRoles
{
    /**
     * List the roles.
     *
     * @var array
     */
    protected $roles = [];

    /**
     * Check if the account has an specific role.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }
}
