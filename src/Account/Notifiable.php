<?php

namespace Basics\Account;

use Illuminate\Notifications\Notifiable as BaseNotifiable;

trait Notifiable
{
    use BaseNotifiable;

    /**
     * Get the email address that should be used for notifications.
     *
     * @return string
     */
    public function getEmailForNotifications()
    {
        return $this->email;
    }
}
