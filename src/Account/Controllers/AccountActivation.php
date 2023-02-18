<?php

namespace Basics\Account\Controllers;

use Basics\Account\Contracts\Activable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait AccountActivation
{
    /**
     * Activates the account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bixopod\Account\Contracts\Activable  $account
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function activateAccount(Request $request, Activable $account)
    {
        $wasActivated = !$account->hasActiveAccount() && $account->activateAccount();

        if ($wasActivated) {
            return $request->wantsJson() //
                ? new JsonResponse([], 202)
                : back()->with('activated', true);
        }

        return $request->wantsJson() //
            ? new JsonResponse([], 204)
            : back()->with('activated', false);
    }

    /**
     * Deactivates the account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bixopod\Account\Contracts\Activable  $account
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function deactivateAccount(Request $request, Activable $account)
    {
        $wasDeactivated = $account->hasActiveAccount() && $account->deactivateAccount();

        if ($wasDeactivated) {
            return $request->wantsJson() //
                ? new JsonResponse([], 202)
                : back()->with('deactivated', true);
        }

        return $request->wantsJson() //
            ? new JsonResponse([], 204)
            : back()->with('deactivated', false);
    }
}
