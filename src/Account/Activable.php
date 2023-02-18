<?php

namespace Basics\Account;

use Basics\Account\Events\Activated;
use Basics\Account\Events\Deactivated;

trait Activable
{
    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    abstract public function hasVerifiedEmail();

    /**
     * Determine if the acount is active.
     *
     * @return bool
     */
    public function hasActiveAccount()
    {
        return $this->hasVerifiedEmail() && $this->active;
    }

    /**
     * Activate the account.
     *
     * @return bool
     */
    public function activateAccount()
    {
        if (!$this->hasVerifiedEmail()) {
            return false;
        }

        $this->active = true;

        if (!$this->save()) {
            return false;
        }

        event(new Activated($this));

        return true;
    }

    /**
     * Deactivate the account.
     *
     * @return bool
     */
    public function deactivateAccount()
    {
        if (!$this->hasVerifiedEmail()) {
            return false;
        }

        $this->active = false;

        if (!$this->save()) {
            return false;
        }

        event(new Deactivated($this));

        return true;
    }
}
