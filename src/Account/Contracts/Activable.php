<?php

namespace Basics\Account\Contracts;

interface Activable
{
    /**
     * Determine if the acount is active.
     *
     * @return bool The account is active.
     */
    public function hasActiveAccount();

    /**
     * Activate the account.
     *
     * @return bool The account was activated.
     */
    public function activateAccount();

    /**
     * Deactivate the account.
     *
     * @return bool The account was deactivated.
     */
    public function deactivateAccount();
}
