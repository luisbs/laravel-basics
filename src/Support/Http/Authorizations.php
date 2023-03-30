<?php

namespace Basics\Support\Http;

use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Arr;

trait Authorizations
{
    /**
     * Authorize a given action for the current user.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = []): Response
    {
        $arguments = $this->parseArguments($ability, $arguments);

        return app(Gate::class)->authorize($ability, $arguments);
    }

    /**
     * Authorize a given action for a user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        $arguments = $this->parseArguments($ability, $arguments);

        return app(Gate::class)->forUser($user)->authorize($ability, $arguments);
    }

    /**
     * Guesses the model argument if is not passed.
     */
    protected function parseArguments(string $ability, $arguments): array
    {
        if (in_array($ability, $this->actionsWithoutModels())) {
            return Arr::prepend(Arr::wrap($arguments), $this->authorizationModel());
        }

        return Arr::wrap($arguments);
    }

    /**
     * Especifies the model to authorize with.
     */
    protected function authorizationModel(): string
    {
        return '';
    }

    /**
     * Get the list of actions which do not have model parameters.
     */
    protected function actionsWithoutModels(): array
    {
        return ['viewAny', 'create'];
    }
}
