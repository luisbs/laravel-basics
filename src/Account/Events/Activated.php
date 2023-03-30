<?php

namespace Basics\Account\Events;

use Illuminate\Queue\SerializesModels;

class Activated
{
    use SerializesModels;

    /**
     * The activated account.
     *
     * @var \Basics\Account\Contracts\Activable
     */
    public $account;

    /**
     * Create a new event instance.
     *
     * @param  \Basics\Account\Contracts\Activable  $account
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
    }
}
