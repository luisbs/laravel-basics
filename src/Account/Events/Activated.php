<?php

namespace Basics\Account\Events;

use Illuminate\Queue\SerializesModels;

class Activated
{
    use SerializesModels;

    /**
     * The activated account.
     *
     * @var \Bixopod\Modules\Users\Models\User
     */
    public $account;

    /**
     * Create a new event instance.
     *
     * @param  \Bixopod\Modules\Users\Models\User  $account
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
    }
}
