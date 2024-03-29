<?php

namespace Basics\Support;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ModelService
{
    /**
     * Underlying instance to work with the service.
     *
     * @var null|\Illuminate\Database\Eloquent\Model
     */
    protected $instance;

    /**
     * Creates a new service.
     *
     * @param  null|\Illuminate\Database\Eloquent\Model  $instance
     */
    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Creates a new service instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return static
     */
    public static function make($instance)
    {
        return new self($instance);
    }

    /**
     * Set the service underlying instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $instance
     * @return static
     */
    public function set($instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * Get the service underlying instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function get()
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
     *
     * @return static
     */
    abstract public static function create(array $attributes);

    /**
     * Updates the attributes of a model.
     *
     * @return static
     */
    public function update(array $attributes)
    {
        $this->throwIfInstanceIsNull();

        $this->instance->fill($attributes)->save();

        return $this;
    }

    /**
     * Destroyes the model and all the related data.
     */
    public function destroy(): bool
    {
        $this->throwIfInstanceIsNull();

        return $this->instance->delete();
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
     */
    public function isEmpty(): bool
    {
        return is_null($this->instance);
    }

    /**
     * Check if the model instance is null.
     *
     * @throws \Exception
     */
    protected function throwIfInstanceIsNull(): void
    {
        if ($this->isEmpty()) {
            throw new \Exception('unknown', 404);
        }
    }
}
