<?php

namespace Basics\Account\Contracts;

interface Notifiable
{
    /**
     * Get the email address that should be used for notifications.
     *
     * @return string
     */
    public function getEmailForNotifications();
}
