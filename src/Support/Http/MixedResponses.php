<?php

namespace Basics\Support\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Provides methods to handle both API and Web responses.
 */
trait MixedResponses
{
    /**
     * Flash a conditional variable to the session.
     *
     * @param  string  $key
     * @param  bool  $condition
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected static function notifySuccess(string $key, bool $condition, Request $request)
    {
        if ($condition) {
            return $request->wantsJson() //
                ? new JsonResponse([], 202)
                : back()->with($key, true);
        }

        return $request->wantsJson() //
            ? new JsonResponse([], 204)
            : back()->with($key, false);
    }
}
