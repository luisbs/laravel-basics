<?php

namespace Basics\Support;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ModelService
{
    /**
     * Underlying instance to work with the service.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $instance;

    /**
     * Creates a new service.
     *
     * @param \Illuminate\Database\Eloquent\Model  $instance
     */
    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Creates a new service instance.
     *
     * @param \Illuminate\Database\Eloquent\Model  $instance
     */
    public static function new($instance)
    {
        return new static($instance);
    }

    /**
     * Set the service underlying instance.
     *
     * @param \Illuminate\Database\Eloquent\Model  $instance
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
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Bixopod\Modules\Share\ApiException
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
     * @param  string  $attribute
     * @param  mixed   $default
     * @return mixed
     */
    public function attr($attribute, $default = null)
    {
        return $this->isEmpty() //
            ? $default
            : $this->instance->getAttribute($attribute, $default);
    }

    /**
     * Creates a new model instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Bixopod\Modules\Share\Support\Service
     */
    abstract public static function create($request);

    /**
     * Updates the attributes of a model.
     *
     * @param \Illuminate\Http\Request  $request
     */
    public function update($request)
    {
        $this->throwIfInstanceIsNull();

        $this->instance->fill($request->all())->save();

        return $this;
    }

    /**
     * Returns a valid HTTP resource response.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function toResource()
    {
        return new JsonResource($this->instance);
    }

    /**
     * Check if the service has null instance.
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
