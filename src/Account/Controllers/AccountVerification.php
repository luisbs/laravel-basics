<?php

namespace Basics\Account\Controllers;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait AccountVerification
{
    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\MustVerifyEmail  $account
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function sendVerificationEmail(Request $request, MustVerifyEmail $account)
    {
        if ($account->hasVerifiedEmail()) {
            return $request->wantsJson() //
                ? new JsonResponse([], 204)
                : back()->with('resent', false);
        }

        $account->sendEmailVerificationNotification();

        return $request->wantsJson() //
            ? new JsonResponse([], 202)
            : back()->with('resent', true);
    }
}
