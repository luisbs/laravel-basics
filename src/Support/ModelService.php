<?php

namespace Basics\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class ModelService
{
    /**
     * Underlying instance to work with the service.
     */
    protected ?Model $instance;

    /**
     * Creates a new service.
     */
    public function __construct(?Model $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Creates a new service instance.
     */
    public static function make(Model $instance): self
    {
        return new self($instance);
    }

    /**
     * Set the service underlying instance.
     */
    public function set(Model $instance): self
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * Get the service underlying instance.
     */
    public function get(): Model
    {
        return $this->instance;
    }

    /**
     * Get the identifier of the instance.
     *
     * @return null|int|string
     */
    public function getKey()
    {
        return $this->isEmpty() //
            ? null
            : $this->instance->getKey();
    }

    /**
     * Get an attribute of the instance.
     *
     * @return mixed
     */
    public function attr(string $key, $default = null)
    {
        return $this->isEmpty() //
            ? $default
            : $this->instance->getAttribute($key, $default);
    }

    /**
     * Creates a new model instance.
     */
    abstract public static function create(array $attributes): self;

    /**
     * Updates the attributes of a model.
     */
    public function update(array $attributes): self
    {
        $this->throwIfInstanceIsNull();

        $this->instance->fill($attributes)->save();

        return $this;
    }

    /**
     * Returns a valid HTTP resource response.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function toResource(): JsonResource
    {
        return new JsonResource($this->instance);
    }

    /**
     * Check if the service has a null instance.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->instance);
    }

    /**
     * Check if the model instance is null.
     *
     * @throws \Exception
     */
    protected function throwIfInstanceIsNull()
    {
        if ($this->isEmpty()) {
            throw new \Exception('unknown', 404);
        }
    }
}
